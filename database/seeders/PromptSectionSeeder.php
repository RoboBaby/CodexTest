<?php

namespace Database\Seeders;

use App\Models\PromptSection;
use Illuminate\Database\Seeder;

class PromptSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['key' => 'identity', 'title' => 'Identity & Context', 'description' => 'Defines who AskGVT is, current date awareness, and how it presents itself.', 'order_index' => 1],
            ['key' => 'product_info', 'title' => 'Product & Model Information', 'description' => 'Describes AskGVT’s model family, API access, and capabilities.', 'order_index' => 2],
            ['key' => 'safety', 'title' => 'Safety, Ethics & Refusal Rules', 'description' => 'Outlines safety principles, refusals, and prohibited content handling.', 'order_index' => 3],
            ['key' => 'style_tone', 'title' => 'Tone, Style & Formatting Guidelines', 'description' => 'Controls tone, empathy, style, formatting, and how lists or bullets are used.', 'order_index' => 4],
            ['key' => 'knowledge_cutoff', 'title' => 'Knowledge Cutoff & Temporal Awareness', 'description' => 'Specifies AskGVT’s reliable knowledge date and how to handle newer information.', 'order_index' => 5],
            ['key' => 'search_instructions', 'title' => 'Search & Tool Usage Instructions', 'description' => 'Defines when and how AskGVT should use search or external tools.', 'order_index' => 6],
            ['key' => 'query_complexity', 'title' => 'Query Complexity & Research Categories', 'description' => 'Describes decision rules for single search vs multi-tool research.', 'order_index' => 7],
            ['key' => 'web_search_guidelines', 'title' => 'Web Search Behaviour', 'description' => 'Details query formation, result selection, and source prioritisation.', 'order_index' => 8],
            ['key' => 'copyright_policy', 'title' => 'Copyright & Legal Requirements', 'description' => 'Sets hard rules for fair use, quoting, and handling of copyrighted material.', 'order_index' => 9],
            ['key' => 'harmful_content', 'title' => 'Harmful & Sensitive Content Policies', 'description' => 'Defines how AskGVT handles hate, violence, child safety, and illegal content.', 'order_index' => 10],
            ['key' => 'search_examples', 'title' => 'Search & Research Examples', 'description' => 'Provides demonstration examples for how AskGVT should perform searches.', 'order_index' => 11],
            ['key' => 'critical_reminders', 'title' => 'Critical Behavioural Reminders', 'description' => 'Key overarching priorities such as respecting copyright and search limits.', 'order_index' => 12],
            ['key' => 'citation_instructions', 'title' => 'Citation & Attribution Rules', 'description' => 'How to cite search sources and structure attributions.', 'order_index' => 13],
            ['key' => 'artifacts_info', 'title' => 'Artifact Creation & Usage Rules', 'description' => 'Defines when to create artifacts, their allowed types, and design principles.', 'order_index' => 14],
            ['key' => 'analysis_tool', 'title' => 'Analysis / REPL Tool Instructions', 'description' => 'Outlines when and how the analysis tool may be used, with supported libraries.', 'order_index' => 15],
            ['key' => 'style_system', 'title' => 'User Style System', 'description' => 'Explains how AskGVT handles <userStyle> and <userExamples> tags.', 'order_index' => 16],
            ['key' => 'closing', 'title' => 'Final Constraints & Behaviour Summary', 'description' => 'Summarises high-level behavioural constraints and prohibited actions.', 'order_index' => 17],
        ];

        foreach ($sections as $section) {
            PromptSection::updateOrCreate(['key' => $section['key']], $section);
        }
    }
}
