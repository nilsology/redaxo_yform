<?php

/**
 * yform
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_yform_value_be_media extends rex_yform_value_abstract
{

    function enterObject()
    {
        static $counter = 0;
        $counter++;

        $this->params['form_output'][$this->getId()] = $this->parse('value.be_media.tpl.php', compact('counter'));

        $this->params['value_pool']['email'][$this->getElement(1)] = $this->getValue();
        if ($this->getElement(4) != 'no_db') {
            $this->params['value_pool']['sql'][$this->getElement(1)] = $this->getValue();
        }
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'be_media',
            'values' => array(
                'name' => array( 'type' => 'name',   'label' => rex_i18n::msg("yform_values_defaults_name")),
                'label' => array( 'type' => 'text',    'label' => rex_i18n::msg("yform_values_defaults_label")),
                'preview'  => array( 'type' => 'checkbox',   'label' => rex_i18n::msg("yform_values_be_media_preview")),
                'multiple'  => array( 'type' => 'checkbox',   'label' => rex_i18n::msg("yform_values_be_media_multiple")),
                'category' => array( 'type' => 'text',   'label' => rex_i18n::msg("yform_values_be_media_category")),
                'types'    => array( 'type' => 'text',   'label' => rex_i18n::msg("yform_values_be_media_types"),   'notice' => rex_i18n::msg("yform_values_be_media_types_notice")),
                'notice'    => array( 'type' => 'text',    'label' => rex_i18n::msg("yform_values_defaults_notice")),
            ),
            'description' => rex_i18n::msg("yform_values_be_media_description"),
            'formbuilder' => false,
            'dbtype' => 'text'
        );
    }


    static function getListValue($params)
    {

        $files = explode(',', $params['subject']);

        if (count($files) == 1) {
            $filename = $params['subject'];
            if (strlen($params['subject']) > 16) {
                $filename = substr($params['subject'], 0, 6) . ' ... ' . substr($params['subject'], -6);
            }
            $return[] = '<span style="white-space:nowrap;" title="' . htmlspecialchars($params['subject']) . '">' . $filename . '</span>';

        } else {
            foreach ($files as $file) {
                $filename = $file;
                if (strlen($file) > 16) {
                    $filename = substr($file, 0, 6) . ' ... ' . substr($file, -6) . '</span>';
                }
                $return[] = '<span style="white-space:nowrap;" title="' . htmlspecialchars($file) . '">' . $filename . '</span>';
            }

        }

        return implode('<br />', $return);

    }

    public static function getSearchField($params)
    {
        $params['searchForm']->setValueField('text', array('name' => $params['field']->getName(), 'label' => $params['field']->getLabel()));
    }

    public static function getSearchFilter($params)
    {
        $sql = rex_sql::factory();
        $value = $params['value'];
        $field =  $params['field']->getName();

        if ($value == '(empty)') {
            return ' (' . $sql->escapeIdentifier($field) . ' = "" or ' . $sql->escapeIdentifier($field) . ' IS NULL) ';

        } elseif ($value == '!(empty)') {
            return ' (' . $sql->escapeIdentifier($field) . ' <> "" and ' . $sql->escapeIdentifier($field) . ' IS NOT NULL) ';

        }

        $pos = strpos($value, '*');
        if ($pos !== false) {
            $value = str_replace('%', '\%', $value);
            $value = str_replace('*', '%', $value);
            return $sql->escapeIdentifier($field) . " LIKE " . $sql->escape($value);
        } else {
            return $sql->escapeIdentifier($field) . " = " . $sql->escape($value);
        }

    }

}
