# Hey Tony
WordPress Avada child theme

## Shortcodes

### `ht-main-nav`
Outputs main navigation. Doesn't accept any parameters. Pulls links from `main-navigation` menu. Pulls logos from Avada settings for `logo` and `sticky_header_logo`.

### `ht-main-footer`
Outputs main footer. Doesn't accept any parameters. Pulls info from `avada-footer-widget-1`, `avada-footer-widget-2` and `avada-footer-widget-3`. Pulls links from `footer_navigation`. Pulls logo from Avada settings for `logo`.

### `ht-archive-title`
Output archive page title. Replace `:` with `–⁠` and wrap first part of title in `div` to be lighter weight. Doesn't accept any parameters.

### `ht-hero-image`
Outputs background image in hero. Doesn't accept any parameters. Doesn't apply to archive pages. Pulls image from `post_thumbnail`.

### `ht-accent`
Outputs text with accent underline where specified.

_Parameters:_

* `text`\
_Type:_ `string`\
_Default:_ `''`

* `accent`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ Text within previous parameter to be underlined.\
_Required_

_Returns:_ `string` of markup

### `ht-swoop`
Outputs curved shaped below hero.

_Parameters:_

* `flip`\
_Type:_ `boolean`\
_Default:_ `false`

* `position`\
_Type:_ `string`\
_Default:_ `bottom`\
_Possible Values:_ `bottom`, `top`

* `featured_image`\
_Type:_ `boolean`\
_Default:_ `false`\
_Note:_ Doesn't apply to archive pages.

_Returns:_ `string` of markup

### `ht-meta`
Outputs meta information associated with single post.

_Parameters:_

* `archive_label`\
_Type:_ `string`\
_Default:_ `''`

* `categories_label`\
_Type:_ `string`\
_Default:_ `''`

* `tags_label`\
_Type:_ `string`\
_Default:_ `''`

* `date_label`\
_Type:_ `string`\
_Default:_ `''`

* `up_date_label`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ Label for date last updated.

* `author_label`\
_Type:_ `string`\
_Default:_ `''`

* `inline`\
_Type:_ `boolean`\
_Default:_ `true`\
_Description:_ Display in a row if `true` and as a column if `false`.

* `border`\
_Type:_ `boolean`\
_Default:_ `false`\
_Description:_ Display pipes in between items in a row.

* `items`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ Comma separated list with possible values `archive`, `category`, `categories`, `tags`, `date`, `author`.\
_Required_

* `justify`\
_Type:_ `string`\
_Default:_ `center`\
_Description:_ Value that corresponds with `justify-content`.

_Returns:_ `string` of markup

### `ht-filters`
Outputs category filters for work or blog posts.

_Parameters:_

* `type`\
_Type:_ `string`\
_Default:_ `post`\
_Possible Values:_ `post`, `work`\
_Required_

_Returns:_ `string` of markup

### `ht-stat`
Outputs large text with accent underline and small text in circular shape overlaid on top of image.

_Parameters:_

* `large_text`\
_Type:_ `string`\
_Default:_ `''`

* `small_text`\
_Type:_ `string`\
_Default:_ `''`

* `align`\
_Type:_ `string`\
_Default:_ `left`\
_Possible Values:_ `left`, `right`

* `wide`\
_Type:_ `boolean`\
_Default:_ `false`\
_Description:_ Full width vs half width image.

_Returns:_ `string` of markup

### `ht-posts`

_Parameters:_

* `type`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ post type.

* `posts_per_page`\
_Type:_ `int`\
_Default:_ `10`

* `category`\
_Type:_ `string`\
_Default:_ `''`

* `meta_key`\
_Type:_ `string`\
_Default:_ `''`

* `meta_value`\
_Type:_ `string`\
_Default:_ `''`

* `meta_type`\
_Type:_ `string`\
_Default:_ `string`\
_Possible Values:_ `string`, `int`

* `ids`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ Comma separated list of post ids to include.

* `section_title`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ Add heading above output of posts.

* `section_title_heading_level`\
_Type:_ `string`\
_Default:_ `'h2'`\

* `a11y_section_title`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ Add visually hidden heading above output of posts for screen readers.

* `heading_level`\
_Type:_ `string`\
_Default:_ `h2`\
_Possible Values:_ `h2`, `h3`\
_Description:_ Heading level for post titles.

* `slider`\
_Type:_ `string`\
_Default:_ `''`\
_Possible Values:_ `''`, `group`, `loop`\

* `current_single`\
_Type:_ `boolean`\
_Default:_ `false`\
_Description:_ Output content for single post within single post. Used in single testimonial.

* `pagination`\
_Type:_ `boolean`\
_Default:_ `false`\
_Description:_ Include pagination at bottom of posts. Meant for use in archive pages.

* `type_archive`\
_Type:_ `boolean`\
_Default:_ `true`\
_Description:_ Set to `false` to override default archive behavior.

_Returns:_ `string` of markup

### `ht-tabs`
Outputs tablist and tabpanels. Content is pulled from Tabs Meta custom fields on pages and service posts. For multiple tabs separate with `***` in List and Panel custom fields. In the List field, information is separated by `:` and maps out to `{label}:{title}:{image id}:{selected}`. In the Panel field, a double line break indicates a new panel and should match the number of items in the List field.

_Parameters:_

* `index`\
_Type:_ `int`\
_Default:_ `0`\
_Description:_ If multiple tabs, specify which one to display based on index starting at 0.

* `half`\
_Type:_ `boolean`\
_Default:_ `false`\
_Description:_ Specify if width is half of container.

_Returns:_ `string` of markup

### `ht-collapsible`
Outputs collapsible and expandable section.

_Parameters:_

* `title`\
_Type:_ `string`\
_Default:_ `''`

* `heading_level`\
_Type:_ `string`\
_Default:_ `h3`\
_Description:_ Heading level for title.

* `accordion`\
_Type:_ `string`\
_Default:_ `''`\
_Description:_ If expect other collapsibles to close when one opens, specify a common string to group them together.

_Returns:_ `string` of markup

### `ht-device`
Outputs bar with three dots to indicate desktop mockup or thick border to indicate mobile mockup.

_Parameters:_

* `type`\
_Type:_ `string`\
_Default:_ `mobile`\
_Possible Values:_ `mobile`, `desktop`\

* `img_id`\
_Type:_ `int`\
_Default:_ `0`\
_Required_

_Returns:_ `string` of markup

## CSS classes

### Base

#### Headings
Used to override default size for semantic heading level.

| Class     | Font-size (mobile → desktop) |
| --------- | ---------------------------- |
| `.h1`     | `48px → 96px`                |
| `.h1-s`   | `48px → 72px`                |
| `.h2-l`   | `38px → 60px`                |
| `.h2`     | `36px → 48px`                |
| `.h3-l`   | `32px → 36px`                |
| `.h3`     | `28px → 32px`                |
| `.h4`     | `24px → 28px`                |
| `.h5`     | `19px → 24px`                |
| `.h6`     | `18px → 20px`                |

#### Paragraphs/text

| Class     | Font-size (mobile → desktop) |
| --------- | ---------------------------- |
| `.p-l`    | `24px → 28px`                |
| `.p`      | `19px → 24px`                |
| `.p-m`    | `16px → 18px`                |
| `.p-s`    | `16px`                       |
| `.p-xs`   | `14px`                       |

### Layout

#### Padding

| Class     | Property          |
| --------- | ----------------- |
| `.l-p-`   | `padding`         |
| `.l-pt-`  | `padding-top`     |
| `.l-pb-`  | `padding-bottom`  |

| Size      | Value        |
| --------- | ------------ |
| `xxxs`    | `20px`       |
| `xxs`     | `30px`       |
| `xs`      | `40px`       |
| `s`       | `60px`       |
| `r`       | `80px`       |
| `m`       | `100px`      |
| `l`       | `120px`      |

| Breakpoint | Value                           |
| ---------- | ------------------------------- |
| `-l`       | `min 1000px` (applies to `xs`+) |

#### Margin

| Class     | Property        |
| --------- | --------------- |
| `.l-m-`   | `margin`        |
| `.l-mb-`  | `margin-bottom` |

| Size      | Value        |
| --------- | ------------ |
| `xxs`     | `5px`        |
| `xs`      | `10px`       |
| `s`       | `15px`       |
| `r`       | `20px`       |
| `m`       | `25px`       |
| `l`       | `40px`       |
| `xl`      | `60px`       |

| Suffix     | Value                   |
| ---------- | ----------------------- |
| `-all`     | Applies to all children |

#### Max widths

| Class          | Description                  |
| -------------- | ---------------------------- |
| `.l-mw-full`   | Viewport width as max for overflow possibility |
| `.l-mw-half`   | Half viewport width as max for overflow possibility in 2 column layout |

### Utilities

| Class       | Description                          |
| ----------- | ------------------------------------ |
| `.u-v-h`    | Visually hide for screen readers     |
| `.u-c-i`    | Set all children to `color: inherit` |
| `.u-bt-1`   | Border top black at 30% opacity      |
| `.u-o-h`    | `overflow: hidden`                   |
| `.u-d-n`    | `display: none`                      |
| `.u-d-b`    | `display: block`                     |
| `.u-d-i`    | `display: inline`                    |
| `.u-d-ib`   | `display: inline-block`              |
| `.u-p-r`    | `position: relative`                 |
| `.u-p-a`    | `position: absolute`                 |
| `.u-l-0`    | `left: 0`                            |
| `.u-r-0`    | `right: 0`                           |
| `.u-b-0`    | `bottom: 0`                          |
| `.u-t-0`    | `top: 0`                             |
| `.u-tlrb-b` | Pseudo element positioned absolutely to fill up container |

### Objects

| Class          | Description                             |
| -------------- | --------------------------------------- |
| `.o-form`      | Wrapper class to style various form elements (inputs, textarea, select, ninja form overrides etc.) |
| `.o-underline` | Underline for links                     |
| `.o-accent`    | Curved line for links and text in spans |
| `.o-tabs`      | Wrapper class for tablist and tabs      |

### Components
Start with `c-`. Best to avoid removing them.

#### `.c-logos`
Display images in wrappable row.

| Class          | Img size                              |
| -------------- | ------------------------------------- |
| default        | `max-width: 180px`	`max-height: 50px` |
| `.c-logos__m`  | `max-height: 40px`                    |
| `.c-logos__s`  | `max-height: 30px`                    |

| Class          | Img size (min 1000px)                 |
| -------------- | ------------------------------------- |
| default        | `max-width: 210px`	`max-height: 70px` |
| `.c-logos__m`  | `max-height: 55px`                    |
| `.c-logos__s`  | `max-height: 40px`                    |

### Themes

| Class                   | Description                                                           |
| ----------------------- | --------------------------------------------------------------------- |
| `.t-text-light`         | Set color for all children to be theme light as well as focus outline |
| `.t-foreground-base`    | Set color to theme black                                              |
| `.t-background-base`    | Set color to theme beige                                              |
| `.t-primary-base`       | Set color to theme blue                                               |
| `.t-bg-background-base` | Set background color to theme beige                                   |
| `.t-bg-background-dark` | Set background color to theme beige dark                              |
| `.t-bg-foreground-base` | Set background color to theme black                                   |
| `.t-ov-background-dark` | Set circular overlap background in cards to theme beige dark          |
| `.t-bg-inherit`         | Inherit background color from parent element                          |

### Overrides
Start with `ht-`. Best to avoid removing them.

#### `.button-outline`
Variant of `.fusion-button` with outline instead of fill.

## Reading settings

In Settings/Reading there are options at the bottom to set the number of posts displayed on archive pages and for pagination.
