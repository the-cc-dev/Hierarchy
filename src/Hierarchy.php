<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Hierarchy;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
class Hierarchy
{

    const FILTERABLE = 1;

    /**
     * @var int
     */
    private $flags = 0;

    /**
     * @var array
     */
    private static $branches = [
        Branch\BranchEmbed::class,
        Branch\Branch404::class,
        Branch\BranchSearch::class,
        Branch\BranchFrontPage::class,
        Branch\BranchHome::class,
        Branch\BranchPostTypeArchive::class,
        Branch\BranchTaxonomy::class,
        Branch\BranchAttachment::class,
        Branch\BranchSingle::class,
        Branch\BranchPage::class,
        Branch\BranchSingular::class,
        Branch\BranchCategory::class,
        Branch\BranchTag::class,
        Branch\BranchAuthor::class,
        Branch\BranchDate::class,
        Branch\BranchArchive::class,
        Branch\BranchPaged::class,
    ];

    /**
     * @param int $flags
     */
    public function __construct($flags = self::FILTERABLE)
    {
        $this->flags = is_int($flags) ? $flags : 0;
    }

    /**
     * Get hierarchy.
     *
     * @param \WP_Query $query
     *
     * @return array
     */
    public function getHierarchy(\WP_Query $query = null)
    {
        return $this->parse($query)->hierarchy;
    }

    /**
     * Get flatten hierarchy.
     *
     * @param \WP_Query $query
     *
     * @return array
     */
    public function getTemplates(\WP_Query $query = null)
    {
        return $this->parse($query)->templates;
    }

    /**
     * Parse all branches.
     *
     * @param \WP_Query $query
     *
     * @return \stdClass
     */
    private function parse(\WP_Query $query = null)
    {
        (is_null($query) && isset($GLOBALS['wp_query'])) and $query = $GLOBALS['wp_query'];

        $data = (object)['hierarchy' => [], 'templates' => [], 'query' => $query];

        $branches = self::$branches;

        // make the branches filterable, but assuring each item still implement branch interface
        if ($this->flags & self::FILTERABLE) {
            $branches = array_filter(
                (array)apply_filters('brain.hierarchy.branches', $branches),
                function ($branch) {
                    return is_subclass_of($branch, Branch\BranchInterface::class, true);
                }
            );
        }

        if ($query instanceof \WP_Query) {
            $data = array_reduce($branches, [$this, 'parseBranch'], $data);
            $data->templates[] = 'index';
            $data->hierarchy['index'] = ['index'];
        }

        $data->templates = array_values(array_unique($data->templates));

        return $data;
    }

    /**
     * @param string    $branchClass
     * @param \stdClass $data
     *
     * @return \stdClass
     */
    private function parseBranch(\stdClass $data, $branchClass)
    {
        /** @var \Brain\Hierarchy\Branch\BranchInterface $branch */
        $branch = new $branchClass();
        $name = $branch->name();
        $isFilterable = ($this->flags & self::FILTERABLE) > 0;
        // When branches are filterable, we need this for core compatibility.
        $isFilterable and $name = preg_replace('|[^a-z0-9-]+|', '', $name);
        if ($branch->is($data->query) && ! isset($data->hierarchy[$name])) {
            $leaves = $branch->leaves($data->query);
            // this filter was introduced in WP 4.7
            $isFilterable and $leaves = apply_filters("{$name}_template_hierarchy", $leaves);
            $data->hierarchy[$name] = $leaves;
            $data->templates = array_merge($data->templates, $leaves);
        }

        return $data;
    }
}
