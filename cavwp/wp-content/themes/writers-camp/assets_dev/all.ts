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

      getRandom(category: 'animal' | 'cidade') {
         const items = {
            animal: [
               'albatroz',
               'avestruz',
               'bisonte',
               'mamute',
               'mandril',
               'orangotango',
               'ornitorrinco',
               'ouriço',
               'panda',
               'pavão',
               'pelicano',
               'pinguim',
               'pônei',
               'porco-espinho',
               'porco',
               'porquinho-da-índia',
               'preá',
               'rinoceronte',
               'suricate',
               'tigre',
               'touro',
               'unicórnio',
               'urso',
            ],
            cidade: [
               'Tóquio',
               'Déli',
               'Xangai',
               'São Paulo',
               'Cidade do México',
               'Cairo',
               'Mumbai',
               'Pequim',
               'Daca',
               'Osaka',
               'Nova Iorque',
               'Carachi',
               'Buenos Aires',
               'Xunquim',
               'Istambul',
               'Calcutá',
               'Manila',
               'Lagos',
               'Tianjin',
               'Quinxassa',
               'Cantão',
               'Los Angeles',
               'Moscou',
               'Shenzhen',
               'Lahore',
               'Bangalor',
               'Paris',
               'Bogotá',
               'Jacarta',
               'Chenai',
               'Lima',
               'Banguecoque',
               'Seul',
               'Nagoia',
               'Haiderabade',
               'Londres',
               'Teerã',
               'Chicago',
               'Chengdu',
               'Nanquim',
               'Wuhan',
               'Luanda',
               'Amedabade',
               'Kuala Lumpur',
               'Xian',
               'Honguecongue',
               'Dongguan',
               'Hancheu',
               'Foshan',
               'Shenyang',
               'Riade',
               'Bagdá',
               'Santiago',
               'Surrate',
               'Madrid',
               'Sucheu',
               'Pune',
               'Harbin',
               'Houston',
               'Dallas',
               'Toronto',
               'Dar es Salaam',
               'Miami',
               'Belo Horizonte',
               'Singapura',
               'Filadélfia',
               'Atlanta',
               'Fukuoka',
               'Cartum',
               'Barcelona',
               'Joanesburgo',
               'São Petersburgo',
               'Qingdao',
               'Dalian',
               'Washington, DC',
               'Rangum',
               'Alexandria',
               'Jinan',
               'Guadalajara',
            ],
         }

         const list = items[category]

         return list[Math.floor(Math.random() * list.length)]
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
