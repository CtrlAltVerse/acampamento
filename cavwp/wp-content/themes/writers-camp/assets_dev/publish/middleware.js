import Blockquote from '@tiptap/extension-blockquote'
import CodeBlock from '@tiptap/extension-code-block'
import Heading from '@tiptap/extension-heading'
import HorizontalRule from '@tiptap/extension-horizontal-rule'
import Paragraph from '@tiptap/extension-paragraph'
import { ListItem } from '@tiptap/extension-list'

const addCommentsAttrs = () => {
   return {
      default: null,
      parseHTML: (element) => element.getAttribute('data-comments'),
      renderHTML: (attributes) => {
         return { 'data-comments': attributes.comments }
      },
   }
}

const cavParagraph = Paragraph.extend({
   addAttributes() {
      return {
         ...this.parent?.(),
         comments: addCommentsAttrs(),
      }
   },
})
const cavBlockquote = Blockquote.extend({
   addAttributes() {
      return {
         ...this.parent?.(),
         comments: addCommentsAttrs(),
      }
   },
})
const cavCodeBlock = CodeBlock.extend({
   addAttributes() {
      return {
         ...this.parent?.(),
         comments: addCommentsAttrs(),
      }
   },
})
const cavHeading = Heading.extend({
   addAttributes() {
      return {
         ...this.parent?.(),
         comments: addCommentsAttrs(),
      }
   },
})
const cavHorizontalRule = HorizontalRule.extend({
   addAttributes() {
      return {
         ...this.parent?.(),
         comments: addCommentsAttrs(),
      }
   },
})
const cavListItem = ListItem.extend({
   addAttributes() {
      return {
         ...this.parent?.(),
         comments: addCommentsAttrs(),
      }
   },
})

export {
   cavParagraph,
   cavBlockquote,
   cavCodeBlock,
   cavHeading,
   cavHorizontalRule,
   cavListItem,
}
