<?php

use cavWP\Models\Post;
use writersCampP\Club\Utils as UtilsClub;
use writersCampP\Utils;

extract($args);

$per_club   = (bool) $challenge->get('per_club');
$sequential = (bool) $challenge->get('sequential');
$count      = (int) $challenge->get('publish_count');

if ($per_club || !empty($text)) {
   $all_clubs = UtilsClub::get();
}

$middle = '<span class="grow flex items-center justify-center pb-9.5 text-xl font-extrabold text-neutral-500 dark:text-neutral-200">' . $key + 1 . '</span>';

if (!empty($text)) {
   $text   = new Post($text);
   $status = $text->get('status');
   $title  = $text->get('title');
   $author = $text->get('author:display_name');
   $link   = $text->get('link');
   $clubs  = $text->get('terms', taxonomy: 'club');
   $mini   = $text->get('image_mini');

   $color     = 'currentColor';
   $club_name = '';

   // RESERVADO
   if ('publish' !== $status) {
      echo <<<HTML
      <div class="relative aspect-banner flex flex-col justify-between cols-1 sm:cols-2 lg:cols-4 rounded-xl border-3 pt-8.5 text-neutral-100 border-neutral-500 cursor-default bg-neutral-900/10 overflow-hidden">
         {$middle}
         <span class="absolute bottom-0 -left-0.75 -right-0.75 z-5 rounded-b-lg pt-2 pb-1.5 px-4 uppercase font-semibold text-xs text-neutral-100 bg-neutral-500"><i class="ri-progress-2-line"></i> Reservado</span>
      </div>
      HTML;

      return;
   }

   // PUBLISH
   if (!empty($clubs)) {
      $color     = $clubs[0]->get('color');
      $club_name = $clubs[0]->get('title');

      echo <<<HTML
      <a href="{$link}" class="relative aspect-square flex flex-col justify-between cols-1 sm:cols-2 lg:cols-4 rounded-xl border-3 text-neutral-100 overflow-hidden" style="background-color: {$color}; border-color: {$color};" title="{$title} por {$author}">
         <div class="bg-neutral-900/30 absolute inset-0 z-1"></div>
         <img class="object-cover object-center absolute z-0 inset-0 size-full" src="{$mini}" alt="" loading="lazy" />
         <div class="relative flex flex-col justify-end mb-10 py-1.5 px-3.5 h-full z-2 text-shadow-lg">
            <span class="font-medium text-md line-clamp-3">{$title}</span>
            <span>{$author}</span>
         </div>
         <span class="absolute bottom-0 -left-0.75 -right-0.75 z-5 rounded-b-lg pt-2 pb-1.5 px-4 uppercase font-semibold text-xs" style="background-color: {$color}">{$club_name}</span>
      </a>
      HTML;

      return;
   }
}

// BLOCKED
if ($sequential && $key > $count) {
   echo '<div></div>';

   return;
}

// EMPTY - PER CLUB
if ($per_club) {
   $color     = $all_clubs[$key]->get('color');
   $club_name = $all_clubs[$key]->get('title');
   $new_url   = Utils::get_page_link('edit', [
      'desafio' => $challenge->ID,
      'slot'    => $key,
      'guilda'  => $all_clubs[$key]->get('ID'),
   ]);

   echo <<<HTML
      <a href="{$new_url}" class="relative aspect-banner flex flex-col justify-between cols-1 sm:cols-2 lg:cols-4 rounded-xl border-3 border-dashed border-b-solid text-neutral-100" style="border-color: {$color}">
         {$middle}
         <span class="absolute bottom-0 -left-0.75 -right-0.75 rounded-b-lg pt-2 pb-1.5 px-4 uppercase font-semibold text-xs" style="background-color: {$color}">{$club_name}</span>
      </a>
   HTML;

   return;
}

$new_url = Utils::get_page_link('edit', [
   'desafio' => $challenge->ID,
   'slot'    => $key,
]);

// EMPTY
echo <<<HTML
   <a href="{$new_url}" class="relative flex aspect-banner flex-col justify-between cols-1 sm:cols-2 lg:cols-4 rounded-xl border-3 border-dashed border-b-solid">
      {$middle}
      <span class="absolute bottom-0 -left-0.75 -right-0.75 rounded-b-lg pt-2 pb-1.5 px-4 uppercase font-semibold text-xs bg-neutral-700 dark:bg-neutral-100 text-neutral-100 dark:text-neutral-700"><i class="ri-add-circle-line"></i> Enviar</span>
   </a>
HTML;
