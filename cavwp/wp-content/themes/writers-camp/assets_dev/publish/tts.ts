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
            name: voice.name.replace('pt-BR-Chirp3-HD-', ''),
            genre: voice.genre,
            file: 'https://cdn.altvers.net/voices/gcp/' + voice.name + '.wav',
         }))
      },
   }))
})
