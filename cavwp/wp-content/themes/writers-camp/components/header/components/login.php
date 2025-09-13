<?php

use cavWP\Form;
use cavWP\Utils as CavWPUtils;
use writersCampP\Writer\Utils;

$Form     = new Form(Utils::get_sign_fields());
$Retrieve = new Form(Utils::get_retrieve_fields());
$networks = CavWPUtils::get_login_networks();

?>
<dialog id="login"
        class="m-auto w-full max-w-2xl rounded-lg backdrop:z-45 backdrop:bg-neutral-900/60 dark:text-neutral-100 dark:bg-neutral-600"
        x-on:click.self="login.close()">
   <div class="flex flex-col sm:grid sm:grid-rows-1 sm:grid-cols-2 h-full min-h-126"
        x-data="{is_signup: true, method: ''}"
        x-effect="method = $store.login.method;if('login'===method){is_signup = false;$store.login.method='email'};if(['google','facebook'].includes(method)){is_signup = true}">
      <div
           class="relative flex flex-col items-center justify-between h-full min-h-40 text-neutral-100 py-3 px-4 text-shadow-xs">
         <img class="absolute inset-0 -z-1 size-full object-cover"
              src="<?php echo get_theme_file_uri('assets/images/camping.jpg'); ?>"
              loading="lazy"
              alt />
         <div class="flex justify-start w-full">
            <h2 class="h2">
               <span x-text="is_signup ? 'Cadastrar' : 'Entrar'"></span>
               <span x-show="$store.login.method==='email'" x-transition:enter x-cloak>com email</span>
               <span x-show="$store.login.method==='google'" x-transition:enter x-cloak>com Google</span>
               <span x-show="$store.login.method==='facebook'" x-transition:enter x-cloak>com Facebook</span>
            </h2>
         </div>
         <div class="hidden sm:flex flex-col gap-5 text-neutral-900 text-center text-sm">
            <div class="rounded-xl py-3 px-5 bg-neutral-100/80">
               Interaja com escritores e leitores
            </div>
            <div class="rounded-xl py-3 px-5 bg-neutral-100/80">
               Exercite a escrita e a criatividade
            </div>
         </div>
         <div>Um projeto <span class="font-medium">CtrlAltVersœ</span></div>
      </div>
      <form class="grow-1 flex flex-col justify-between gap-3 py-3 px-4"
            x-on:submit.prevent="doLogin"
            x-show="$store.login.method!=='retrieve'">
         <button class="self-end flex justify-center items-center rounded bg-neutral-300/10 hover:bg-neutral-100 hover:text-neutral-700 focus-visible:bg-neutral-300 size-8 text-lg cursor-pointer"
                 type="button" x-on:click.prevent="login.close()">
            <i class="ri-close-fill"></i>
         </button>
         <ul class="flex flex-col items-center gap-3" x-show="$store.login.method==='intro'" x-transition:enter
             x-cloak>
            <?php foreach ($networks as $key => $network) { ?>
            <?php if ('email' === $key) {
               $attr = 'x-on:click.prevent="$store.login.method=\'email\'" role="button"';
            } else {
               $attr = "x-login:{$key}.init=\"{$network['login']}\" data-redirect=\"{$network['redirect']}\"";

               if ('google' === $key) {
                  $attr .= ' data-theme="filled_black" data-text="continue_with" data-size="large" data-shape="pill" data-width=264';
               }

               if ('facebook' === $key) {
                  $attr .= ' data-width=264 data-button-type="continue_with" data-size="large" data-layout="rounded" data-onlogin="handleFbToken" data-scope="public_profile, email"';
               }
            } ?>
            <template x-if=$store.login.method!==''>
               <li>
                  <button class="btn !rounded-full w-66" type="button"
                          <?php echo $attr; ?>>
                     <i
                        class="<?php echo $network['icon']; ?> ri-lg"></i>
                     Continuar com
                     <?php echo $network['name']; ?>
                  </button>
               </li>
            </template>
            <?php } ?>
         </ul>
         <div class="flex flex-col justify-center gap-3" x-show="$store.login.method!=='intro'" x-transition:enter
              x-cloak>
            <div class="flex flex-col gap-1" x-show="is_signup" x-transition:enter x-cloak>
               <?php $Form->label('display_name', ['class' => 'font-medium']); ?>
               <?php $Form->field('display_name', [
                  'class' => 'border border-neutral-500 rounded py-1 px-2',
               ]); ?>
            </div>
            <div class="flex flex-col gap-1" x-show="is_signup" x-transition:enter x-cloak>
               <?php $Form->label('user_login', ['class' => 'font-medium'], p_attrs: [
                  'class' => 'text-sm',
               ]); ?>
               <?php $Form->field('user_login', [
                  'class'           => 'border border-neutral-500 rounded py-1 px-2',
                  'placeholder'     => 'usuario',
                  'autocomplete'    => 'username',
                  'x-bind:required' => 'is_signup',
               ]); ?>
            </div>
            <div class="flex flex-col gap-1">
               <?php $Form->label('user_email', ['class' => 'font-medium'], p_attrs: false); ?>
               <?php $Form->field('user_email', [
                  'class'               => 'border border-neutral-500 rounded py-1 px-2 readonly:bg-neutral-500',
                  'x-bind:placeholder'  => 'is_signup && "Não ficará público"',
                  'x-bind:readonly'     => '$store.login.method!=="email"',
                  'x-bind:autocomplete' => '$store.login.method!=="email" && "off"',
               ]); ?>
            </div>
            <template x-if="$store.login.method==='email'">
               <div class="flex flex-col gap-1">
                  <?php $Form->label('user_pass', ['class' => 'font-medium'], p_attrs: false); ?>
                  <?php $Form->field('user_pass', [
                     'class'               => 'border border-neutral-500 rounded py-1 px-2',
                     'x-bind:placeholder'  => 'is_signup && "Use uma senha segura"',
                     'x-bind:autocomplete' => 'is_signup ? "new-password" : "current-password"',
                  ]); ?>
               </div>
            </template>
            <div class="flex justify-between" x-show="$store.login.method==='email'" x-cloak>
               <label class="cursor-pointer font-medium" for="is_signup"
                      x-text="is_signup ? 'Fazer login' : 'Cadastrar-se'"></label>
               <button class="cursor-pointer" type="button" x-show="!is_signup"
                       x-on:click.prevent="user_email.value.length<3 ? $do('toast','','Preencha o e-mail.') : $rest.post(moon.apiUrl+'/retrieve', {user_email: user_email.value})"
                       x-transition:enter x-cloak>Recuperar senha</button>
            </div>
            <button class="btn" type="submit">Entrar</button>
         </div>
         <div class="flex justify-center gap-3">
            <button class="cursor-pointer" type="button" x-show="$store.login.method!=='intro'" x-cloak
                    x-on:click.prevent="$store.login.method='intro'">Voltar</button>
            <a href="<?php echo get_privacy_policy_url(); ?>"
               target="_black">Termos</a>
         </div>
         <?php $Form->field('is_signup', [
            'x-model' => 'is_signup',
            'class'   => 'hidden',
         ]); ?>
         <?php $Form->field('social_user_id', [
            'type' => 'hidden',
         ]); ?>
         <?php $Form->field('sign_method', [
            'type'    => 'hidden',
            'x-model' => 'method',
         ], 'input'); ?>
      </form>
      <form class="grow-1 flex flex-col justify-between gap-3 py-3 px-4"
            x-show="$store.login.method==='retrieve'"
            x-on:submit.prevent="$rest.post(moon.apiUrl+'/new_pass')">
         <button class="flex justify-center items-center rounded bg-neutral-300/10 hover:bg-neutral-100 hover:text-neutral-700 focus-visible:bg-neutral-300 size-8 text-lg cursor-pointer"
                 type="button" x-on:click.prevent="login.close()">
            <i class="ri-close-fill"></i>
         </button>
         <h2 class="h2">
            Redefinir senha
         </h2>
         <div class="flex flex-col justify-center gap-3">
            <div class="flex flex-col gap-1">
               <?php $Retrieve->label('rp_pass', ['class' => 'font-medium'], p_attrs: false); ?>
               <?php $Retrieve->field('rp_pass', [
                  'class'       => 'border border-neutral-500 rounded py-1 px-2',
                  'placeholder' => 'Use uma senha segura',
               ]); ?>
               <?php $Retrieve->field('rp_key', [
                  'type' => 'hidden',
               ]); ?>
               <?php $Retrieve->field('rp_login', [
                  'type' => 'hidden',
               ]); ?>
            </div>
            <button class="btn" type="submit">Salvar</button>
         </div>
         <button class="cursor-pointer" type="button" x-on:click.prevent="login.close()">Cancelar</button>
      </form>
      <input id="token" type="hidden" />
   </div>
</dialog>
