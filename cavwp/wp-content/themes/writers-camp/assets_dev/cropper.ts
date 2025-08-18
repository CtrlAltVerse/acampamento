import Masonry from 'masonry-layout'
import Croppie from 'croppie'

document.addEventListener('alpine:init', () => {
   Alpine.data('cropper', function () {
      return {
         fWidth: 1896,
         fHeight: 1017,
         mWidth: 640,
         mHeight: 343,
         mainUrl:
            'https://unsplash.com/?utm_source=CtrlAltVersœ&utm_medium=referral',
         search: {
            q: '',
            page: 1,
            maxPages: 0,
            req: {} as cavRestResponse,
            loading: false,
         },
         cropper: {
            croppie: null as Croppie,
            zoom: 0,
            picked: false as unsplashItem,
         },
         selected: {
            color: '#ffffff',
            image_author: '',
            image_author_url: '',
            image_full: '',
            image_mini: '',
         },

         init() {
            this.$watch('search.q', () => {
               this.search.page = 1
            })

            this.$watch('cropper.picked', (picked) => {
               if (!picked) {
                  return
               }
               this.setImage()
            })
         },

         async doSearch() {
            if (this.search.q.length < 3) {
               return
            }

            this.search.loading = true

            this.search.maxPages = 0

            this.search.req = await this.$rest.get(
               sky.unsplashUrl + '/search',
               { q: this.search.q, page: this.search.page }
            )

            this.$nextTick(() => {
               setTimeout(() => {
                  this.initGallery()
                  this.search.loading = false
                  this.search.maxPages = this.search.req.data.maxPages
               }, 222)
            })
         },

         setPage(sum: number) {
            const newPage = this.search.page + sum
            if (
               newPage === 0 ||
               newPage === this.search.maxPages ||
               this.search.q.length < 3
            ) {
               return
            }
            this.search.page = newPage

            return this.doSearch()
         },

         initGallery() {
            new Masonry(this.$refs.results, {
               gutter: 4 * 2,
               columnWidth: 'li',
               itemSelector: 'li',
            })
         },

         setImage() {
            if (!this.cropper.picked) {
               return
            }

            if (this.cropper.croppie instanceof Croppie) {
               this.cropper.croppie.destroy()
            }

            const media = document.getElementById('media')
            const dialog = document.getElementById('unsplash')

            this.selected.image_author = this.cropper.picked.image_author
            this.selected.image_author_url =
               this.cropper.picked.image_author_url

            this.cropper.croppie = new Croppie(media, {
               viewport: { width: 728, height: 391 },
               boundary: { width: dialog.offsetWidth - 34, height: 550 },
               enableOrientation: true,
               showZoomer: false,
            })

            let cropWidth: number

            this.cropper.croppie
               .bind({
                  url: `${this.cropper.picked.raw}&q=75&w=${this.fWidth}`,
                  zoom: 0,
               })
               .then(() => {
                  //@ts-expect-error
                  const zoom = this.cropper.croppie._currentZoom
                  //@ts-expect-error
                  cropWidth = this.cropper.croppie._originalImageWidth

                  this.cropper.zoom = zoom
                  this.$refs.zoom.setAttribute('min', `${zoom}`)

                  media.appendChild(this.setTitle())
               })

            media.addEventListener('update', (e) => {
               if (!this.cropper.picked) {
                  return
               }

               const [topLeftX, topLeftY, bottomRightX, bottomRightY] =
                  //@ts-expect-error
                  e.detail.points
               //@ts-expect-error
               const orient = e.detail.orientation

               const ratio = this.cropper.picked.width / cropWidth
               const x = (topLeftX * ratio).toFixed(0)
               const y = (topLeftY * ratio).toFixed(0)
               const w = ((bottomRightX - topLeftX) * ratio).toFixed(0)
               const h = ((bottomRightY - topLeftY) * ratio).toFixed(0)

               this.selected.image_full = `${this.cropper.picked.raw}&auto=format&fm=jpg&cs=tinysrgb&orient=${orient}&fit=crop&rect=${x},${y},${w},${h}&w=${this.fWidth}&h=${this.fHeight}&q=85`

               this.selected.image_mini = `${this.cropper.picked.raw}&auto=format&fm=jpg&cs=tinysrgb&orient=${orient}&fit=crop&rect=${x},${y},${w},${h}&w=${this.mWidth}&h=${this.mHeight}&q=80`
            })

            this.$watch('cropper.zoom', (zoom) => {
               this.cropper.croppie.setZoom(zoom)
            })
         },

         setTitle() {
            const titleEl = document.getElementById(
               'post_title'
            ) as HTMLInputElement
            const summaryEl = document.getElementById(
               'post_excerpt'
            ) as HTMLInputElement

            const card = document.createElement('div')
            card.classList = 'media-card'
            const title = document.createElement('div')
            title.classList = 'media-title'
            title.textContent = titleEl.value.length
               ? titleEl.value
               : titleEl.placeholder
            card.appendChild(title)

            const summary = document.createElement('div')
            summary.classList = 'media-summary'
            summary.textContent = summaryEl.value.length
               ? summaryEl.value
               : summaryEl.placeholder
            card.appendChild(summary)

            return card
         },

         rotate(angle: 90 | -90) {
            this.cropper.croppie?.rotate(angle)
            this.cropper.croppie.setZoom(this.cropper.zoom)
         },

         async confirm() {
            if (this.cropper.picked === false) {
               return
            }

            this.search.req = await this.$rest.get(
               sky.unsplashUrl + '/download',
               { url: this.cropper.picked.download }
            )

            Object.entries(this.selected).forEach(([key, value]) => {
               const el = document.querySelector(
                  `input[name="${key}"]`
               ) as HTMLInputElement

               el.value = value

               if (key === 'image_mini') {
                  el.dispatchEvent(new InputEvent('input'))
               }
            })
         },
      }
   })
})
