import { Extension } from '@tiptap/core'

declare module '@tiptap/core' {
   interface Commands<ReturnType = any> {
      cav: {
         moveUp: () => ReturnType
         moveDown: () => ReturnType
      }
   }
}

export default Extension.create({
   name: 'cav',

   addCommands() {
      return {
         moveUp:
            () =>
            ({ state, commands }) => {
               const { $from, $to, from, to } = state.selection
               const startParent = $from.index(-1)

               if (startParent === 0) {
                  return false
               }

               const $doc = $from.node(-1)
               const $before = $doc.child(startParent - 1)
               const $shift = $before.nodeSize

               commands.cut(
                  { from: $from.before(), to: $to.after() },
                  $from.before() - $shift
               )

               if (from === to) {
                  commands.focus(from - $shift)
               } else {
                  commands.setTextSelection({
                     from: from - $shift,
                     to: to - $shift,
                  })
               }

               return true
            },
         moveDown:
            () =>
            ({ state, commands }) => {
               const { $from, $to, from, to } = state.selection
               const endParent = $to.index(-1)
               const $doc = $from.node(-1)

               if (endParent === $doc.childCount - 1) {
                  return false
               }

               const $after = $doc.child(endParent + 1)
               const $shift = $after.nodeSize

               commands.cut(
                  { from: $from.before(), to: $to.after() },
                  $to.after() + $shift
               )

               if (from === to) {
                  commands.focus(from + $shift)
               } else {
                  commands.setTextSelection({
                     from: from + $shift,
                     to: to + $shift,
                  })
               }

               return true
            },
      }
   },

   addKeyboardShortcuts() {
      return {
         'Mod-Alt-ArrowUp': () => this.editor.commands.moveUp(),
         'Mod-Alt-ArrowDown': () => this.editor.commands.moveDown(),
      }
   },
})
