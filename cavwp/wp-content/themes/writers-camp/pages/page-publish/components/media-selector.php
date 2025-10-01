<?php

?>
<dialog id="unsplash" x-data="cropper"
   class="m-auto overflow-y-auto backdrop:bg-neutral-900/60 border-2 border-neutral-900 dark:text-neutral-100 dark:bg-neutral-600"
   x-on:click.self="unsplash.close()">
   <search class="flex flex-col gap-3 w-full sm:w-160 md:w-3xl lg:w-5xl xl:w-7xl text-lg" x-show="!cropper.picked">
      <h2 class="sr-only">Escolha uma imagem para ilustrar o texto</h2>
      <form x-on:submit.prevent="doSearch" class="flex border-b-2 border-dark">
         <div class="flex items-center w-full my-1 mx-2 gap-2">
            <input class="grow py-2 px-3.5 font-semibold outline-none" type="search" name="q" x-model="search.q"
               placeholder="Busque por uma imagem" />
            <button class="py-2 px-3.5 bg-neutral-700 text-neutral-100 cursor-pointer" type="submit" title="Buscar">
               <i class="ri-search-line"></i>
            </button>
            <div class="flex items-center gap-2 py-2 px-3.5" x-show="search.loading">
               <i class="ri-loader-4-fill animate-spin"></i>
               Carregando
            </div>
            <div class="py-2 px-3.5" x-show="search.maxPages > 0">
               Página <span x-text="search.page"></span> de <span x-text="search.maxPages"></span>
            </div>
            <button class="py-2 px-3.5 bg-neutral-700 text-neutral-100 cursor-pointer" type="button" x-on:click="setPage(-1)" title="Página anterior" x-show="search.maxPages > 0" x-bind:disabled="search.page===1">
               <i class="ri-arrow-left-line"></i>
            </button>
            <button class="py-2 px-3.5 bg-neutral-700 text-neutral-100 cursor-pointer" type="button" x-on:click="setPage(1)" title="Próxima página" x-show="search.maxPages > 0" x-bind:disabled="search.page===search.maxPages">
               <i class="ri-arrow-right-line"></i>
            </button>
            <button class="py-2 px-3.5 bg-neutral-700 text-neutral-100 cursor-pointer" type="button" x-on:click="unsplash.close()"
               title="Próxima página" x-bind:disabled="search.page===search.maxPages">
               <i class="ri-close-fill"></i>
            </button>
         </div>
      </form>
      <ul class="min-h-96 mx-2" x-ref="results">
         <template x-for="media in search.req?.data?.results ?? []">
            <li class="v-gap-2 cols-2 sm:cols-3 md:cols-4 xl:cols-5 group">
               <button class="w-full relative cursor-pointer" type="button" x-on:click="cropper.picked=media">
                  <img class="w-full" alt="" loading="lazy" x-bind:src="media.thumb">
                  <span class="absolute bottom-2 left-2 py-1 px-2 text-shadow-md/100 text-neutral-100 text-left text-md hidden group-hover:inline" x-text="media.image_author"></span>
               </button>
            </li>
         </template>
      </ul>
   </search>
   <section class="flex flex-col gap-3 w-full sm:w-160 md:w-3xl lg:w-5xl xl:w-7xl text-lg" x-show="!!cropper.picked">
      <div class="flex border-b-2 border-dark">
         <div class="flex items-center w-full my-1 mx-2 gap-2">
            <h2 class="grow py-2 px-3.5 font-semibold">Posicione a imagem</h2>
            <button class="py-2 px-3.5 bg-neutral-700 text-neutral-100 cursor-pointer" type="button"
               x-on:click="cropper.picked=false" title="Voltar">
               <i class="ri-arrow-left-s-line"></i>
            </button>
            <button class="py-2 px-3.5 bg-neutral-700 text-neutral-100 cursor-pointer" type="button"
               x-on:click="confirm();unsplash.close()" title="Confirmar">
               <i class="ri-checkbox-circle-fill"></i>
            </button>
         </div>
      </div>
      <form class="flex flex-col mx-2">
         <div id="media" class="relative" x-bind:style="`color: ${selected.color==1?'#000':'#fff'}`"></div>
         <div class="flex gap-3">
            <div class="flex flex-col gap-1">
               <label class="font-semibold" for="cropper-zoom">
                  Zoom: <span x-text="Number(cropper.zoom).toFixed(2)"></span>
               </label>
               <input id="cropper-zoom" type="range" max="1.3" step="0.05" x-model="cropper.zoom" x-ref="zoom" />
            </div>
            <div class="flex flex-col gap-1">
               <strong>Girar</strong>
               <div class="flex gap-3">
                  <button class="btn small whitespace-nowrap" type="button" x-on:click="rotate(90)">
                     <i class="ri-anticlockwise-line"></i>
                     -90º
                  </button>
                  <button class="btn small whitespace-nowrap" type="button" x-on:click="rotate(-90)">
                     <i class="ri-clockwise-line"></i>
                     90º
                  </button>
               </div>
            </div>
            <div class="flex flex-col gap-1">
               <strong>Cor do Texto</strong>
               <div class="flex flex-wrap gap-3">
                  <label for="color-white" class="btn small !flex items-center gap-2 cursor-pointer">
                     <input id="color-white" class="hidden" name="color" x-model="selected.color" value="0" type="radio" />
                     <span class="inline-block rounded size-4.25" style="background: #fff"></span>
                     Branco
                  </label>
                  <label for="color-black" class="btn small !flex items-center gap-2 cursor-pointer">
                     <input id="color-black" class="hidden" name="color" x-model="selected.color" value="1" type="radio" />
                     <span class="inline-block rounded size-4.25" style="background: #000"></span>
                     Preto
                  </label>
               </div>
            </div>
         </div>
      </form>
   </section>
   <a class="flex items-center gap-2 mt-3 mx-2" x-bind:href="!cropper.picked ? mainUrl : cropper.picked.image_author_url"
      target="_blank" rel="external">
      <i class="ri-unsplash-fill ri-2x"></i>
      <span x-show="!cropper.picked">Photos by</span>
      <span x-show="cropper.picked">Photo by <span x-text="cropper.picked.image_author"></span> on</span>
      <span class="font-unsplash font-bold text-lg">Unsplash</span>
   </a>
</dialog>
