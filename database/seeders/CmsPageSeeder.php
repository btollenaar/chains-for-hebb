<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About the Park',
                'slug' => 'about-the-park',
                'template' => 'default',
                'excerpt' => 'Learn about Hebb County Park and its beautiful setting along the Willamette River.',
                'content' => '<h2>Hebb County Park</h2>
<p>Hebb County Park is a scenic 38-acre park located in West Linn, Oregon, along the banks of the Willamette River. Managed by Clackamas County, the park offers a boat ramp, fishing dock, picnic areas, and approximately 0.5 miles of nature trails through native Douglas fir and Oregon white oak forests.</p>

<h2>Why Disc Golf Here?</h2>
<p>The park\'s natural terrain — rolling hills, mature trees, and open meadows — is ideal for a disc golf course. The existing trail system provides natural corridors for fairways, and the tree canopy offers both shade and technical challenge.</p>

<h2>Location</h2>
<p>Hebb County Park is located at the end of Hebb Park Road in West Linn, OR 97068, just minutes from downtown West Linn and Oregon City. The park is accessible year-round during daylight hours.</p>',
                'is_published' => true,
                'show_in_nav' => true,
                'sort_order' => 1,
                'meta_title' => 'About Hebb County Park — Chains for Hebb',
                'meta_description' => 'Learn about Hebb County Park in West Linn, Oregon — a beautiful Willamette River park that will soon be home to an 18-hole disc golf course.',
            ],
            [
                'title' => 'The Course Plan',
                'slug' => 'course-plan',
                'template' => 'course-plan',
                'excerpt' => 'Our proposed 18-hole disc golf course design that works with the natural landscape.',
                'content' => '<h2>Course Overview</h2>
<p>The proposed 18-hole course will use the park\'s natural terrain to create a fun, challenging, and accessible disc golf experience for players of all skill levels.</p>

<h3>Design Principles</h3>
<ul>
<li><strong>Work with nature:</strong> Minimal tree removal; use existing clearings and trails</li>
<li><strong>Mixed difficulty:</strong> Holes ranging from beginner-friendly to technically challenging</li>
<li><strong>Natural tee pads:</strong> Compacted gravel pads that blend with the environment</li>
<li><strong>Quality baskets:</strong> DISCatcher tournament-quality baskets on all 18 holes</li>
<li><strong>Clear signage:</strong> Tee signs with hole maps, distances, and par</li>
</ul>

<h3>Construction Phases</h3>
<p><strong>Phase 1 (Front 9):</strong> Holes 1-9, using the more accessible lower portion of the park near the parking area. Estimated completion: Summer 2026.</p>
<p><strong>Phase 2 (Back 9):</strong> Holes 10-18, extending into the hillside trails with more elevation change. Estimated completion: Fall 2026.</p>',
                'is_published' => true,
                'show_in_nav' => true,
                'sort_order' => 2,
                'meta_title' => 'Course Plan — Chains for Hebb',
                'meta_description' => 'See our proposed 18-hole disc golf course design for Hebb County Park, featuring natural tee pads, quality baskets, and terrain-integrated fairways.',
            ],
            [
                'title' => 'How to Help',
                'slug' => 'how-to-help',
                'template' => 'how-to-help',
                'excerpt' => 'There are many ways to support Chains for Hebb — donate, volunteer, sponsor, or spread the word.',
                'content' => '<h2>Every Bit Helps</h2>
<p>Building a disc golf course takes a village. Whether you can contribute financially, physically, or simply by spreading the word, your support makes a difference.</p>

<p>Contact us at <a href="mailto:hello@chainsforhebb.org">hello@chainsforhebb.org</a> to learn more about getting involved.</p>',
                'is_published' => true,
                'show_in_nav' => true,
                'sort_order' => 3,
                'meta_title' => 'How to Help — Chains for Hebb',
                'meta_description' => 'Donate, volunteer, sponsor a hole, or spread the word — here\'s how you can help build a disc golf course at Hebb County Park.',
            ],
            [
                'title' => 'FAQ',
                'slug' => 'faq',
                'template' => 'faq',
                'excerpt' => 'Frequently asked questions about the Chains for Hebb project.',
                'content' => '<h3>What is disc golf?</h3>
<p>Disc golf is a flying disc sport where players throw specialized discs at a series of metal baskets. Like traditional golf, the goal is to complete each hole in the fewest throws. It\'s free to play (once a course exists), family-friendly, and great exercise.</p>

<h3>Why Hebb Park?</h3>
<p>Hebb County Park has ideal terrain for disc golf — mature trees, gentle hills, and open meadows. The park is underutilized compared to its potential, and a disc golf course would bring new visitors while respecting the natural environment.</p>

<h3>How much does it cost?</h3>
<p>Our budget is $15,000 for a complete 18-hole course. This covers quality baskets, tee pads, signage, trail clearing, and course design. See our Progress page for a detailed breakdown.</p>

<h3>Are donations tax-deductible?</h3>
<p>We are working to establish 501(c)(3) status. In the meantime, all donors receive a receipt for their records.</p>

<h3>When will the course be finished?</h3>
<p>Our target is to complete the front 9 by Summer 2026 and the full 18-hole course by Fall 2026, depending on fundraising progress and volunteer availability.</p>

<h3>How can I volunteer?</h3>
<p>Check our Events page for upcoming work parties. We need help with trail clearing, gravel work, basket installation, and more. No experience necessary!</p>',
                'is_published' => true,
                'show_in_nav' => true,
                'sort_order' => 4,
                'meta_title' => 'FAQ — Chains for Hebb',
                'meta_description' => 'Answers to common questions about the Chains for Hebb disc golf course project at Hebb County Park.',
            ],
        ];

        foreach ($pages as $page) {
            CmsPage::create($page);
        }
    }
}
