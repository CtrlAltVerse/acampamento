declare const { Editor }: typeof import('@tiptap/core')

declare const Alpine: typeof import('alpinejs')

interface Window {
   Alpine: typeof Alpine
   Tiptap: Editor
   FB: any
   handleFbToken: (response: any) => void
   handleGoogleToken: (response: any) => void
}

interface cavRestResponse {
   success: boolean
   status: number
   data: any
   headers: cavBasicObj
   transition: ViewTransition | null
}

interface iUnsplashItem {
   width: number
   height: number
   thumb: string
   raw: string
   download: string
   image_author: string
   image_author_url: string
}

type unsplashItem = iUnsplashItem | false

interface iSky {
   unsplashUrl: string
   blocks: any[]
   align: any[]
   marks: any[]
   autosave: string
}

interface iMoon {
   apiUrl: string
   nonce: string
}

interface iEditor {
   post_content: string
   edit_url: string
}

declare
{
   var sky: iSky
   var moon: iMoon
   var editor: iEditor
   var allVoices: any[]
}
