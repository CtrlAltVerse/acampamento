<ul class="flex gap-3 text-sm">
   <li><span x-text="current.words"></span> palavra<span x-text="current.words === 1 ? '' : 's'"></span></li>
   <li><button class="cursor-pointer" type="button" x-on:click.prevent="document.getElementById('shortcuts').showModal()">
         <i class="ri-information-2-fill"></i>
         <span class="hidden md:inline">Teclas de atalho</span>
         <span class="inline md:hidden ">Atalhos</span>
      </button></li>
</ul>
