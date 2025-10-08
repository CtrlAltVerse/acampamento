<?php

use writersCampP\Utils;

?>
<div>
   <a class="btn" href="<?php echo Utils::get_page_link('dashboard'); ?>">
      <i class="ri-arrow-left-line"></i>
      <span class="hidden sm:inline">Voltar</span>
   </a>
   <button class="btn" type="button" title="Salvar (Ctrl+S)" x-on:click.prevent="save('draft')"
      x-on:keydown.ctrl.s.window.prevent="save('draft')" x-bind:disabled="current.saved===0">
      <i class="ri-save-3-fill"></i>
      <span class="hidden sm:inline">Salvar</span>
   </button>
   <button class="btn" type="submit" x-bind:disabled="current.saved===0">
      <i class="ri-file-check-fill"></i>
      <span class="hidden sm:inline">Enviar</span>
   </button>
</div>
