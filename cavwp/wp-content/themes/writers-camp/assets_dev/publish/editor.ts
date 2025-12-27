// todo: IF LISTA/quote, mover item. ou se for first|last mover real root

import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import {
   Focus,
   CharacterCount,
   Placeholder,
   Selection,
} from '@tiptap/extensions'
import TextAlign from '@tiptap/extension-text-align'
import Superscript from '@tiptap/extension-superscript'
import Subscript from '@tiptap/extension-subscript'
import FloatingMenu from '@tiptap/extension-floating-menu'
import Typography from '@tiptap/extension-typography'
// import BubbleMenu from '@tiptap/extension-bubble-menu'

import BubbleMenu from './bubbleMenu.js'
import CavExtension from './extension'
import jsonToBlocks from './utils'

document.addEventListener('alpine:init', () => {
   Alpine.data('typewriter', function () {
      return {
         editor: null as null | (() => Editor),
         current: {
            type: 'paragraph',
            icon: 'ri-paragraph',
            position: 'first',
            showChanger: false,
            has: [],
            alignable: true,
            focus: Alpine.$persist([]),
            words: 0,
            saved: 1, // -1 = not, 0 = saving , 1 = saved
         },
         entry: {
            title: '',
            summary: '',
            json: null as any,
            html: '',
            image_mini: '',
            is_blocks: editor.raw_json !== null,
         },

         init() {
            if (editor.raw_json === null) {
               this.entry.html = editor.post_content
            } else {
               this.entry.json = editor.raw_json
            }

            window.onbeforeunload = (e: any) => {
               if (this.current.saved === -1) {
                  e.preventDefault()
                  return 'Tem certeza?'
               }
            }

            this.$watch('current.showChanger', (state) => {
               if (state) {
                  this.updateCurrent('close')
               } else {
                  this.updateCurrent()
               }
            })

            this.$watch('entry.title', this.setDirt.bind(this))
            this.$watch('entry.summary', this.setDirt.bind(this))
            this.$watch('entry.html', this.setDirt.bind(this))
            this.$watch('entry.image_mini', this.setDirt.bind(this))

            setInterval(() => {
               if (this.current.saved === -1) {
                  this.save('draft')
               }
            }, Number.parseInt(sky.autosave) * 999)

            this.editor = this.initEditor()
            this.countWords()
         },

         setDirt(newS: string, oldS: string) {
            if (oldS === newS) {
               return
            }
            this.current.saved = -1
         },

         save(status = 'pending') {
            if (
               !['pending', 'draft'].includes(status) ||
               this.current.saved === 0
            ) {
               return
            }

            if (
               status === 'draft' &&
               // @ts-expect-error
               document.getElementById('post_title').value.length < 3 &&
               this.entry.html.length < 3
            ) {
               return
            }

            this.current.saved = 0

            const el = document.getElementById('editorForm') as HTMLFormElement
            const formData = new FormData(el)

            formData.append('raw_json', JSON.stringify(this.entry.json))
            formData.append('post_content', this.entry.html)

            let body = {}
            formData.forEach((value, key) => {
               if (String(value).length === 0) {
                  return
               }

               body[key] = value
            })

            this.$rest
               .post(`${moon.apiUrl}/${status}?_wpnonce=${moon.nonce}`, body)
               .then(({ data }) => {
                  this.current.saved = 1

                  if (data.length === 0) {
                     return
                  }

                  data.forEach((action) => {
                     if (action.action === 'value') {
                        history.replaceState(
                           null,
                           '',
                           editor.edit_url.replace('ID', action.content)
                        )
                     }
                  })
               })
         },

         countWords() {
            this.current.words = this.editor().storage.characterCount.words()
         },

         updateCurrent(typeOrEditor: string | Editor = '') {
            if (typeOrEditor === 'close') {
               this.current.icon = 'ri-close-line'
               return
            }

            let type = this.current.type
            this.current.alignable = true

            if (typeOrEditor instanceof Editor) {
               const { $from } = typeOrEditor.state.selection

               if (0 === $from.depth) {
                  this.current.icon = 'ri-more-fill'
                  return
               }

               let n = 0
               type = 'initial'
               let $doc: any

               do {
                  type = $from.node(n === 0 ? null : n).type.name
                  if (
                     ['bulletList', 'orderedList', 'codeBlock'].includes(type)
                  ) {
                     this.current.alignable = false
                  }
                  n--
                  $doc = $from.node(n)
               } while ($doc?.type?.name !== 'doc')

               const index = $from.index(-1)

               if (index === 0) {
                  this.current.position = 'first'
               } else if (index === $doc.childCount - 1) {
                  this.current.position = 'last'
               } else {
                  this.current.position = 'middle'
               }

               this.current.has = []
               sky.align.forEach((align) => {
                  if (typeOrEditor.isActive({ textAlign: align.attr })) {
                     this.current.has.push(align.attr)
                  }
               })
               sky.marks.forEach((mark) => {
                  if (typeOrEditor.isActive(mark.name)) {
                     this.current.has.push(mark.name)
                  }
               })
            }

            const item = sky.blocks.find((el) => {
               return el.name == type
            })

            this.current.type = type
            this.current.icon = item.icon
         },

         mark(mark: string) {
            if (mark.length === 0 || !this.editor()) {
               return
            }
            this.editor().chain().focus().toggleMark(mark).run()
         },

         node(node: string, lvl: 2 | 3 = 2) {
            if (node.length === 0 || !this.editor()) {
               return
            }

            switch (node) {
               case 'heading':
                  this.editor()
                     .chain()
                     .focus()
                     .toggleHeading({ level: lvl })
                     .run()
                  break
               case 'blockquote':
                  this.editor().chain().focus().toggleBlockquote().run()
                  break
               case 'bulletList':
                  this.editor().chain().focus().toggleBulletList().run()
                  break
               case 'orderedList':
                  this.editor().chain().focus().toggleOrderedList().run()
                  break
               case 'horizontalRule':
                  this.editor().chain().focus().setHorizontalRule().run()
                  break
               case 'codeBlock':
                  this.editor().chain().focus().toggleCodeBlock().run()
                  break
               default:
                  this.editor().chain().focus().setParagraph().run()
                  break
            }

            this.current.showChanger = false
         },

         align(align: string) {
            this.editor().chain().focus().toggleTextAlign(align).run()
            this.current.showChanger = false
         },

         move(up = true) {
            if (up) {
               this.editor().chain().focus().moveUp().run()
            } else {
               this.editor().chain().focus().moveDown().run()
            }
            this.current.showChanger = false
         },

         initEditor() {
            let autofocus = true
            if (this.current.focus.length) {
               if (this.current.focus[0] === this.current.focus[1]) {
                  autofocus = this.current.focus[0]
               } else {
                  autofocus = false
               }
            }

            const BubbleMenuEl: HTMLElement =
               document.querySelector('.menu-mark')

            const gEditor = new Editor({
               editorProps: {
                  attributes: {
                     class: 'content editor',
                  },
               },
               autofocus,
               content: this.entry.json ?? this.entry.html,
               element: document.querySelector('#editor'),
               extensions: [
                  StarterKit.configure({
                     heading: {
                        levels: [2, 3],
                     },
                     horizontalRule: {
                        HTMLAttributes: {
                           class: 'hr',
                        },
                     },
                  }),
                  TextAlign.configure({
                     types: ['heading', 'paragraph'],
                     defaultAlignment: 'justify',
                  }),
                  Placeholder.configure({
                     placeholder: ({ editor, node }) => {
                        if (editor.isEmpty) {
                           return 'Comece aqui...'
                        }

                        const item = sky.blocks.find((el) => {
                           return el.name == node.type.name
                        })

                        if (item) {
                           return item.placeholder
                        }

                        return '...'
                     },
                  }),
                  FloatingMenu.configure({
                     element: document.querySelector('.menu-block'),
                     options: {
                        placement: 'left',
                     },
                     shouldShow: ({ editor }) => {
                        return editor.isFocused
                     },
                  }),
                  BubbleMenu.configure({
                     element: BubbleMenuEl,
                     options: {
                        placement: 'bottom',
                        strategy: 'absolute',
                        offset: 8,
                        flip: false,
                        shift: false,
                        arrow: false,
                        size: false,
                        autoPlacement: false,
                        hide: false,
                        inline: false,
                     },
                     shouldShow: ({ editor }) => {
                        const { state } = editor
                        const { empty } = state.selection

                        return (
                           editor.isFocused &&
                           !empty &&
                           !editor.isActive('horizontalRule')
                        )
                     },
                  }),
                  Typography,
                  Selection,
                  CavExtension,
                  Superscript,
                  Subscript,
                  CharacterCount,
                  Focus,
               ],
               onCreate: ({ editor }) => {
                  if (this.entry.is_blocks) {
                     this.entry.html = jsonToBlocks(editor.getJSON())
                  } else {
                     this.entry.html = editor.getHTML()
                  }
               },
               onUpdate: ({ editor }) => {
                  this.entry.json = editor.getJSON()
                  if (this.entry.is_blocks) {
                     this.entry.html = jsonToBlocks(this.entry.json)
                  } else {
                     this.entry.html = editor.getHTML()
                  }

                  this.countWords()
                  this.updateCurrent(editor)
                  this.current.saved = -1
               },
               onSelectionUpdate: ({ editor }) => {
                  this.updateCurrent(editor)
               },
            })

            if (this.current.focus.length) {
               const [from, to] = this.current.focus
               gEditor.commands.setTextSelection({ from, to })
            }

            gEditor.on('selectionUpdate', ({ editor }) => {
               const { from, to } = editor.state.selection
               this.current.focus = [from, to]
               this.updateCurrent(editor)
            })

            return () => {
               return gEditor
            }
         },
      }
   })
})
