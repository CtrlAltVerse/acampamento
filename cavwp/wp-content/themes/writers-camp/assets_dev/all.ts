import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import cav from '@ctrlaltvers/alpine'

Alpine.plugin(cav)
Alpine.plugin(persist)

window.Alpine = Alpine

Alpine.data('bonfire', function () {
   return {
      midPoint: 160,

      init() {
         this.$watch('$store.login.method', (method) => {
            if (['google', 'facebook'].includes(method)) {
               this.checkUser()
            }
         })

         const url = new URL(location.href)

         if (url.searchParams.get('action')) {
            const action = url.searchParams.get('action')
            if ('rp' === action) {
               //@ts-expect-error
               this.$store.login.method = 'retrieve'
               //@ts-expect-error
               document.getElementById('login').showModal()

               //@ts-expect-error
               document.getElementById('rp_key').value =
                  url.searchParams.get('key')

               //@ts-expect-error
               document.getElementById('rp_login').value =
                  url.searchParams.get('login')

               history.replaceState(null, '', url.origin + url.pathname)
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
            'https://api.altvers.net/api/random/v1/' + query
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
         this.$rest.post(moon.apiUrl + '/enter').then((res) =>
            res.data.forEach((action) => {
               if (action.action !== 'ignore') {
                  return
               }

               if (action.target === 'update_nonce') {
                  moon.nonce = action.content
               }
            })
         )
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
         const token = document.getElementsByName('token').value

         return this.$rest.post(moon.apiUrl + '/check', {
            //@ts-expect-error
            sign_method: this.$store.login.method,
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
   document.getElementsByName('token').value = response.credential
}

window.handleFbToken = (response) => {
   if (!response.authResponse) {
      return
   }

   //@ts-expect-error
   Alpine.store('login').method = 'facebook'
   //@ts-expect-error
   document.getElementsByName('token').value = response.authResponse.accessToken
}

// document.addEventListener('AppleIDSignInOnSuccess', (event) => {})

Alpine.start()
