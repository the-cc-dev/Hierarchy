<?php
/*
 * This file is part of the Hierarchy package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brain\Hierarchy\Branch;

use Brain\Hierarchy\PostTemplates;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
final class BranchPage implements BranchInterface
{

    /**
     * @var \Brain\Hierarchy\PostTemplates
     */
    private $postTemplates;

    /**
     * @param \Brain\Hierarchy\PostTemplates|null $postTemplates
     */
    public function __construct(PostTemplates $postTemplates = null)
    {
        $this->postTemplates = $postTemplates ?: new PostTemplates();
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return 'page';
    }

    /**
     * {@inheritdoc}
     */
    public function is(\WP_Query $query)
    {
        return $query->is_page();
    }

    /**
     * {@inheritdoc}
     */
    public function leaves(\WP_Query $query)
    {
        /** @var \WP_Post $post */
        $post = $query->get_queried_object();

        $post instanceof \WP_Post or $post = new \WP_Post((object) ['ID' => 0]);
        $pagename = $query->get('pagename');

        if (empty($post->post_name) && empty($pagename)) {
            return ['page'];
        }

        $name = $pagename ? $pagename : $post->post_name;

        $leaves = ["page-{$name}"];
        $post->ID and $leaves[] = "page-{$post->ID}";
        $leaves[] = 'page';

        if ($post->post_type === 'page') {
            $template = $this->postTemplates->findFor($post);
            $template and array_unshift($leaves, $template);
        }

        return $leaves;
    }
}
