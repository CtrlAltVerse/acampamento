const jsonToBlock = (block: any, parentTag = '') => {
   const { attrs, text, type } = block
   let { content } = block

   if (Array.isArray(content)) {
      content = content
         .map((subBlock: any) => jsonToBlock(subBlock, type))
         .join('')
   }

   if (typeof content === 'undefined' && typeof text === 'undefined') {
      content = ''
   }

   switch (type) {
      case 'paragraph':
         if ('listItem' === parentTag) {
            return content
         }

         const alignP = attrs.textAlign ?? 'justify'
         return `<!-- wp:paragraph {"align":"${alignP}"} -->
<p class="has-text-align-${alignP}">${content}</p>
<!-- /wp:paragraph -->`

      case 'doc':
         return content

      case 'text':
         const marks = block.marks ?? []
         let tags = []

         if (marks.find((i) => i.type === 'bold')) {
            tags.push('strong')
         }
         if (marks.find((i) => i.type === 'italic')) {
            tags.push('em')
         }
         if (marks.find((i) => i.type === 'strike')) {
            tags.push('s')
         }
         if (marks.find((i) => i.type === 'superscript')) {
            tags.push('sup')
         }
         if (marks.find((i) => i.type === 'subscript')) {
            tags.push('sub')
         }
         if (marks.find((i) => i.type === 'code')) {
            tags.push('code')
         }

         let prefix = ''
         let suffix = ''

         if (tags.length) {
            prefix += tags.map((tag) => `<${tag}>`).join('')
            suffix += tags
               .reverse()
               .map((tag) => `</${tag}>`)
               .join('')
         }

         if (marks.find((i) => i.type === 'underline')) {
            prefix += '<span style="text-decoration: underline;">'
            suffix = '</span>' + suffix
         }

         return prefix + text + suffix

      case 'heading':
         const alignH = attrs.textAlign ?? 'left'
         const level = attrs.level ?? 2
         return `<!-- wp:heading {"textAlign":"${alignH}","level":${level}} -->
<h${level} class="wp-block-heading has-text-align-${alignH}">testes</h${level}>
<!-- /wp:heading -->`

      case 'blockquote':
         return `<!-- wp:quote -->
<blockquote class="wp-block-quote">${content}</blockquote>
<!-- /wp:quote -->`

      case 'bulletList':
      case 'orderedList':
         const isOrdered = 'orderedList' === type
         const tagList = isOrdered ? 'ol' : 'ul'
         const attrsList = isOrdered ? '{"ordered": true}' : ''

         return `<!-- wp:list ${attrsList} -->
<${tagList} class="wp-block-list">${content}</${tagList}>
<!-- /wp:list -->`

      case 'listItem':
         return `<!-- wp:list-item -->
<li>${content}</li>
<!-- /wp:list-item -->`

      case 'codeBlock':
         return `<!-- wp:code -->
<pre class="wp-block-code"><code>${content}</code></pre>
<!-- /wp:code -->`

      case 'horizontalRule':
         return `<!-- wp:separator -->
<hr class="wp-block-separator has-alpha-channel-opacity"/>
<!-- /wp:separator -->`

      default:
         console.log(block)
         return ''
   }
}

export default jsonToBlock
