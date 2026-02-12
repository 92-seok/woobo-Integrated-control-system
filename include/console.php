<?php
class Console
{
    private $outputs = [];

    public function log(...$txt)
    {
        array_push($this->outputs, [
            'type' => 'log',
            'value' => $txt,
        ]);
    }

    public function info(...$txt)
    {
        array_push($this->outputs, [
            'type' => 'info',
            'value' => $txt,
        ]);
    }

    public function error(...$txt)
    {
        array_push($this->outputs, [
            'type' => 'error',
            'value' => $txt,
        ]);
    }

    public function format($arg)
    {
        if (gettype($arg) === 'string') {
            $return = sprintf('"%1$s"', $arg);
        } elseif (gettype($arg) === 'boolean') {
            if ($arg) {
                $return = 'true';
            } else {
                $return = 'false';
            }
        } elseif (gettype($arg) === 'array') {
            $return = '(function(){var ary=[];';
            foreach ($arg as $key => $data) {
                $return .= sprintf('ary["%1$s"]=%2$s;', $key, $this->format($data));
            }
            $return .= 'return ary;})()';
        } elseif (gettype($arg) === 'object') {
            $return = '{';
            foreach ($arg as $key => $data) {
                $return .= sprintf('"%1$s":%2$s;', $key, $this->format($data));
            }
            $return .= '}';
        } elseif (gettype($arg) === 'resource') {
            $return = '"[resource Resource]"';
        } else {
            $return = $arg;
        }

        return $return;
    }

    public function output_script($tab = '', $tab_count = 0, $tab_size = 0)
    {
        if (!$tab_size > 0) {
            if ($tab === "\t") {
                $tab_size = 1;
            } elseif ($tab === ' ') {
                $tab_size = 4;
            } elseif ($tab === '  ' || $tab === '    ') {
                $tab_size = 1;
            } else {
                $tab_size = 0;
            }
        }

        echo "<script>\n";

        foreach ($this->outputs as $id => $value) {
            $args = '';

            for ($i = 0; $i < count($value['value']); $i++) {
                if ($i === 0) {
                    $args .= $this->format($value['value'][$i]);
                } else {
                    $args .= ', ' . $this->format($value['value'][$i]);
                }
            }

            printf('%3$sconsole.%1$s( %2$s );' . "\n", $value['type'], $args, str_repeat($tab, $tab_size * ($tab_count + 1)));
        }

        echo str_repeat($tab, $tab_size * $tab_count) . "</script>\n";
    }
}
