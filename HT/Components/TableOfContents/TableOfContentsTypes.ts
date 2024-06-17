/**
 * Components - TableOfContents Types
 */

/**
 * @typedef {object} TableOfContentsHashArgs
 * @prop {Element|null} link
 * @prop {Element[]} item
 * @prop {number} offset
 */
export interface TableOfContentsHashArgs {
  link: Element | null
  item: Element[]
  offset: number
}
