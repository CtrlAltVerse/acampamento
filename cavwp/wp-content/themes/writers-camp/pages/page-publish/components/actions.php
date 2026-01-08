<?php

use writersCampP\Utils;

?>
<div class="text-md sm:text-lg">
   <a class="btn" href="<?php echo Utils::get_page_link('dashboard'); ?>">
      <i class="ri-arrow-left-line"></i>
      <span class="hidden md:inline">Voltar</span>
   </a>
   <button class="btn" type="button" title="Salvar (Ctrl+S)" x-on:click.prevent="save('draft')"
      x-on:keydown.ctrl.s.window.prevent="save('draft')" x-bind:disabled="entry.saved===0">
      <i class="ri-save-3-fill"></i>
      <span class="hidden md:inline">Salvar</span>
   </button>
   <button class="btn" type="submit" x-bind:disabled="entry.saved===0">
      <i class="ri-file-check-fill"></i>
      <span class="hidden md:inline">Enviar</span>
   </button>
</div>
