<form class="flex" method="get" action="<?php echo home_url() ?>">
   <input class="grow py-2.5 px-3 focus:bg-neutral-200/45" name="s" type="search" value="<?php echo get_search_query() ?>" placeholder="Fazer uma busca" />
   <button class="flex items-center justify-center rounded py-2.5 px-3 aspect-square bg-neutral-100 dark:bg-neutral-500 cursor-pointer" type="submit"><i class="ri-search-line"></i></button>
</form>
