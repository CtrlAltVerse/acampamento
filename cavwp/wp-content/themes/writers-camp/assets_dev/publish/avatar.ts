document.addEventListener('alpine:init', () => {
   Alpine.data('avatar', function () {
      return {
         uploadAvatar(e: SubmitEvent) {
            //@ts-expect-error
            const body = new FormData(e.target)

            fetch(moon.apiUrl + '/avatar?_wpnonce=' + moon.nonce, {
               method: 'POST',
               body,
            }).then((response) => {
               response.json().then((json) => {
                  json.forEach((action) => {
                     this.$do(action)
                  })
               })
            })
         },
      }
   })
})
