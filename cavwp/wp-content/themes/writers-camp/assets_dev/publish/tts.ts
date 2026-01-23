document.addEventListener('alpine:init', () => {
   Alpine.data('tts', () => ({
      genre: 'M',
      voices: [],

      init() {
         this.$watch('genre', () => {
            this.sortVoices()
         })

         this.sortVoices()
      },

      sortVoices() {
         this.voices = allVoices.map((voice) => ({
            id: voice.name,
            name: voice.name.replace('pt-BR-Chirp3-HD-', ''),
            genre: voice.genre,
            file: 'https://cdn.altvers.net/voices/gcp/' + voice.name + '.wav',
         }))
      },

      async requestAudio(e) {
         const globalForm = new FormData(
            document.getElementById('global') as HTMLFormElement,
         )
         const voice = globalForm.get('voice') as string

         if (!voice) {
            alert('Selecione uma voz')
            return
         }
         const btn = e.target.querySelector('[type="submit"]')
         btn.classList.add('!hidden')

         const formItem = new FormData(e.target)

         const title = globalForm.get('title') as string
         const rate = globalForm.get('rate') as string
         const text = formItem.get('text') as string
         const number = formItem.get('number') as string

         const body = {
            voice,
            text,
            title,
            number,
            rate,
         }

         try {
            const request = await this.$rest.post(
               `${moon.apiUrl}/tts?_wpnonce=${moon.nonce}`,
               body,
            )
            const { content, filename } = request.data
            const audio = e.target.querySelector('audio')
            audio.classList.remove('hidden')
            audio.innerHTML = `<source src="data:audio/ogg;base64,${content}" type="audio/ogg" />`

            const byteString = atob(content)
            const bytes = new Uint8Array(byteString.length)
            for (let i = 0; i < byteString.length; i++) {
               bytes[i] = byteString.charCodeAt(i)
            }

            const blob = new Blob([bytes], { type: 'audio/ogg' })
            const url = URL.createObjectURL(blob)

            const a = document.createElement('a')
            a.href = url
            a.download = filename
            a.click()

            URL.revokeObjectURL(url)
         } catch (_error) {}

         btn.classList.remove('!hidden')
      },
   }))
})
