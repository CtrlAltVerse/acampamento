import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import cav from '@ctrlaltvers/alpine'

Alpine.plugin(cav)
Alpine.plugin(persist)

window.Alpine = Alpine

Alpine.data('bonfire', function () {
   return {
      currentUrl: '',
      bookmark: Alpine.$persist(null),
      midPoint: 160,

      init() {
         const Url = new URL(location.href)
         this.currentUrl = Url.origin + Url.pathname

         this.enableBookmark()
         this.checkInitialLetter()

         this.$watch('$store.login.method', (method) => {
            if (['google', 'facebook'].includes(method)) {
               this.checkUser()
            }
         })

         if (Url.searchParams.get('action')) {
            if (document.body.classList.contains('logged-in')) {
               return
            }

            const action = Url.searchParams.get('action')
            if ('rp' === action) {
               //@ts-expect-error
               this.$store.login.method = 'retrieve'
               //@ts-expect-error
               document.getElementById('login').showModal()

               //@ts-expect-error
               document.getElementById('rp_key').value =
                  Url.searchParams.get('key')

               //@ts-expect-error
               document.getElementById('rp_login').value =
                  Url.searchParams.get('login')

               history.replaceState(null, '', Url.origin + Url.pathname)
            }
            if ('login' === action) {
               //@ts-expect-error
               this.$store.login.method = 'login'
               //@ts-expect-error
               document.getElementById('login').showModal()
            }
            if ('signin' === action) {
               //@ts-expect-error
               this.$store.login.method = 'intro'
               //@ts-expect-error
               document.getElementById('login').showModal()
            }
         }
      },

      checkInitialLetter() {
         if (!document.body.classList.contains('single-text')) {
            return
         }

         const allBlocks = document.querySelectorAll(
            '#content > *'
         ) as NodeListOf<HTMLElement>

         if (
            'P' !== allBlocks[0].tagName ||
            allBlocks[0].clientHeight < 70 ||
            allBlocks[0].style.textAlign !== 'justify'
         ) {
            return
         }

         const firstParagraph = allBlocks[0].textContent
         let spaceBreak = 0
         if (firstParagraph.slice(0, 1) === '—') {
            spaceBreak = 2
         }

         let rawInitialLetters = firstParagraph.slice(
            0,
            firstParagraph.indexOf(' ', spaceBreak)
         )

         // if (rawInitialLetters.length > 2 + spaceBreak) {
         //    rawInitialLetters = rawInitialLetters.slice(0, 1 + spaceBreak)
         // }

         const initialLetter =
            '<span class="initial-letter">' + rawInitialLetters + '</span>'

         allBlocks[0].innerHTML = allBlocks[0].innerHTML.replace(
            rawInitialLetters,
            initialLetter
         )

         allBlocks[0].outerHTML = allBlocks[0].outerHTML.replace(
            '<p',
            '<p class="p-with-initial-letter"'
         )
      },

      enableBookmark() {
         if (!document.body.classList.contains('single-text')) {
            return
         }

         document.querySelectorAll('#content > *').forEach((el, item) => {
            const block = `block-${item}`
            el.id = block

            if (
               this.bookmark !== null &&
               this.currentUrl === this.bookmark.url &&
               block === this.bookmark.block
            ) {
               el.classList.add('actual-bookmark')
            }

            el.addEventListener('click', (elEvent) => {
               if (
                  this.bookmark !== null &&
                  this.currentUrl === this.bookmark.url &&
                  this.bookmark.block === block
               ) {
                  this.cleanBookmark()
                  return
               }

               this.cleanBookmark()

               this.bookmark = { url: this.currentUrl, block }

               //@ts-expect-error
               elEvent.target.classList.add('actual-bookmark')
            })
         })

         if (location.hash === '#bookmark') {
            this.openBookmark()
         }
      },

      cleanBookmark() {
         document
            .querySelectorAll('#content > *')
            .forEach((singleBlock) =>
               singleBlock.classList.remove('actual-bookmark')
            )

         this.bookmark = { url: '', block: '' }

         history.pushState(null, '', this.currentUrl)
      },

      openBookmark() {
         if (0 === this.bookmark.url.length) {
            return
         }

         if (this.bookmark.url !== this.currentUrl) {
            location.href = this.bookmark.url + '#bookmark'
         }

         setTimeout(() => {
            this.$do('scroll', '#' + this.bookmark.block)
         }, 111)
      },

      toggleTheme() {
         const color = document.documentElement.classList.contains('dark')
            ? 'light'
            : 'dark'

         if (color.length) {
            localStorage.setItem('theme', color)
         } else {
            localStorage.removeItem('theme')
         }
         //@ts-expect-error
         changeTheme()
      },

      copy(url: string) {
         this.$do([
            { action: 'copy', content: url },
            { action: 'toast', content: 'Link copiado!' },
         ])
      },

      async getRandom(query: string) {
         const { data } = await this.$rest.get(
            'https://ctrl.altvers.net/api/random/v1/' + query
         )
         const sorted = data[Math.floor(Math.random() * data.length)]

         return sorted.name
      },

      async getYoutube(videoID: string) {
         const link = `https://www.youtube.com/watch?v=${videoID}`
         const { data } = await this.$rest.get(
            `https://youtube.com/oembed?url=${link}&format=json`
         )

         let { title } = data
         title = title.replace(/\(.*\)|\[.*\]/i, '').trim()

         return `<a href="${link}" target="_blank"><i class="ri-youtube-fill"></i> ${title}</a>`
      },

      checkTitle() {
         if (!document.body.classList.contains('single-text')) {
            return
         }

         if (null === document.querySelector('.fullscreen-image')) {
            this.$refs.singleTitle.classList.add('!static')
            return
         }

         const elem = document.getElementById('content')
         const rect = elem.getBoundingClientRect()
         const midPoint = rect.top <= this.midPoint
         const small = window.innerWidth <= 1280

         if (midPoint || small) {
            this.$refs.singleTitle.classList.add('!static')
         } else {
            this.$refs.singleTitle.classList.remove('!static')
         }
      },

      doLogin() {
         this.$rest.post(moon.apiUrl + '/enter')
      },

      openMenu() {
         const menu = document.getElementById('menu') as HTMLDialogElement
         menu.classList.add('closing')
         menu.showModal()
         menu.classList.remove('closing')
      },

      closeMenu() {
         const menu = document.getElementById('menu') as HTMLDialogElement
         menu.classList.add('closing')
         setTimeout(() => {
            menu.close()
            menu.classList.remove('closing')
         }, 333)
      },

      checkUser() {
         //@ts-expect-error
         const token = document.getElementById('token').value

         return this.$rest.post(moon.apiUrl + '/check?_wpnonce=' + moon.nonce, {
            //@ts-expect-error
            sign_method: this.$store.login.method,
            //@ts-expect-error
            join: document.getElementById('join') ? true : false,
            token,
         })
      },
   }
})

Alpine.store('login', {
   method: '',
})

window.handleGoogleToken = (response) => {
   //@ts-expect-error
   Alpine.store('login').method = 'google'
   //@ts-expect-error
   document.getElementById('token').value = response.credential
}

window.handleFbToken = (response) => {
   if (!response.authResponse) {
      return
   }

   //@ts-expect-error
   Alpine.store('login').method = 'facebook'
   //@ts-expect-error
   document.getElementById('token').value = response.authResponse.accessToken
}

// document.addEventListener('AppleIDSignInOnSuccess', (event) => {})

Alpine.start()
