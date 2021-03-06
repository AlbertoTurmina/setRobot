<?php

function attachment_field_credit($formFields, $post)
{
    $formFields['setrobot-photographer-name'] = array(
        'label' => 'Créditos da foto',
        'input' => 'text',
        'value' => get_post_meta($post->ID, 'setrobot_copyright_name', true),
        'helps' => 'Se houver crédito da foto.',
    );

    $formFields['setrobot-photographer-url'] = array(
        'label' => 'Site do fotografo',
        'input' => 'text',
        'value' => get_post_meta($post->ID, 'setrobot_copyright_url', true),
        'helps' => 'Site do fotografo',
    );

    return $formFields;
}
add_filter('attachment_fields_to_edit', 'attachment_field_credit', 10, 2);

function attachment_field_credit_save($post, $attachment)
{
    if (isset($attachment['setrobot-photographer-name'])) {
        update_post_meta($post['ID'], 'setrobot_copyright_name', $attachment['setrobot-photographer-name']);
    }

    if (isset($attachment['setrobot-photographer-url'])) {
        update_post_meta($post['ID'], 'setrobot_copyright_url', esc_url($attachment['setrobot-photographer-url']));
    }

    return $post;
}
add_filter('attachment_fields_to_save', 'attachment_field_credit_save', 10, 2);

function img_caption_shortcode_filter($val, $attr, $content = null)
{
    global $post;

    extract(shortcode_atts(array(
        'id'      => '',
        'align'   => '',
        'width'   => '',
        'caption' => ''
    ), $attr));

    if (1 > (int) $width || empty($caption)) {
        return $val;
    }

    $id_img = !$id ?: substr($id, strpos($id, '_') + 1);

    $figcaption_author = '';

    if (copyrightData($id_img, 'name')) {
        $figcaption_author = '<figcaption id="figauthor_' . $id . "_" . $post->ID . '" class="author-figure">';

        if (copyrightData($id_img, 'url')) {
            $figcaption_author .= '<a target="_blank" href="' . copyrightData($id_img, 'url') . '">';
            $figcaption_author .= copyrightData($id_img, 'name');
            $figcaption_author .= '</a>';
        } else {
            $figcaption_author .= copyrightData($id_img, 'name');
        }

        $figcaption_author .= '</figcaption>';
    }

    return '<figure id="' . $id . "_" . $post->ID . '" class="figure-attachment-post '. $attr['align'] .'" style="width:' . (0 + (int) $width) . 'px">' . $figcaption_author . do_shortcode($content) . '<figcaption class="caption-figure" id="figcaption_' . $id . "_" . $post->ID . '" class="wp-caption-text">' . $caption . '</figcaption></figure>';
}
add_filter('img_caption_shortcode', 'img_caption_shortcode_filter', 10, 3);
