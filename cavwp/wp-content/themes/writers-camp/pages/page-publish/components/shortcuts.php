<dialog id="shortcuts" class="m-auto w-full max-w-md rounded-lg backdrop:z-45 backdrop:bg-neutral-900/60 bg-yellow-100 text-neutral-700 dark:text-neutral-100 dark:bg-neutral-700" x-on:click.self="document.getElementById('shortcuts').close()">
   <div class="py-3 px-4" x-data="{parseKeys: (input) => input.split('+').map(key => `<kbd>${key}</kbd>`).join('+')}">
      <div class="flex justify-between items-center mb-6">
         <h2 class="h2">Teclas de atalho</h2>
         <button class="text-xl py-1 px-2 cursor-pointer" title="Fechar" type="button" x-on:click.prevent="document.getElementById('shortcuts').close()">
            <i class="ri-close-line"></i>
         </button>
      </div>
      <ul class="flex flex-col gap-0.75 *:flex *:justify-between">
         <li>
            Salvar rascunho <span class="flex gap-0.5"><kbd>Ctrl</kbd>+<kbd>S</kbd></span>
         </li>
         <template x-for="{label, shortcut} in sky.align">
            <li>
               <span x-text="label"></span> <span class="flex gap-0.5" x-html="parseKeys(shortcut)"></span>
            </li>
         </template>
         <template x-for="{label, shortcut} in sky.blocks">
            <template x-if="!!shortcut">
               <li>
                  <span x-text="label"></span> <span class="flex gap-0.25" x-html="parseKeys(shortcut)"></span>
               </li>
            </template>
         </template>
         <template x-for="{label, shortcut} in sky.marks">
            <template x-if="!!shortcut">
               <li>
                  <span x-text="label"></span> <span class="flex gap-0.5" x-html="parseKeys(shortcut)"></span>
               </li>
            </template>
         </template>
      </ul>
   </div>
</dialog>
