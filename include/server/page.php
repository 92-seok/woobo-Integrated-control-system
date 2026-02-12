<?php
class INSERT_PAGE
{
    public $nowPage;
    public $totalRow;
    public $limit;
    public $listCnt;

    public $pageNum = 25;
    public $blockNum = 10;

    private $totalPage;
    private $totalBlock;
    private $nowBlock;

    function __construct($page, $totalRow, $pageNum = 0, $blockNum = 0)
    {
        $this->nowPage = $page;
        $this->totalRow = $totalRow;

        if ($pageNum !== 0) {
            $this->pageNum = $pageNum;
        }
        if ($blockNum !== 0) {
            $this->blockNum = $blockNum;
        }

        $this->totalPage = ceil($this->totalRow / $this->pageNum);
        $this->totalBlock = ceil($this->totalPage / $this->blockNum);
        $this->nowBlock = ceil($this->nowPage / $this->blockNum);

        $count = ($this->nowPage - 1) * $this->pageNum;
        $this->limit[0] = $count ?? 0;
        $this->limit[1] = $this->pageNum;

        $this->listCnt = $totalRow - $count;
    }

    function getListNum()
    {
        $rtv = ($this->nowPage - 1) * $this->pageNum + 1;
        return $rtv;
    }

    function getPage(): array
    {
        $rtv = [];

        $s = ($this->nowBlock - 1) * $this->blockNum + 1;

        $e_page = $this->nowBlock * $this->blockNum;
        if ($e_page >= $this->totalPage) {
            $e = $this->totalPage;
        } else {
            $e = $e_page;
        }

        for ($p = $s; $p <= $e; $p++) {
            array_push($rtv, $p);
        }

        return $rtv;
    }

    function PBlock()
    {
        $s_page = ($this->nowBlock - 1) * $this->blockNum;

        if ($this->nowBlock != 1) {
            return $s_page;
        } else {
            return false;
        }
    }

    function NBlock()
    {
        $e_page = $this->nowBlock * $this->blockNum + 1;

        if ($this->totalBlock != 0 && $this->nowBlock != $this->totalBlock) {
            return $e_page;
        } else {
            return false;
        }
    }

    function inPage($page, $url): string
    {
        $rtv = '';

        $rtv .= "<div class='cs_page'>";
        // 이전 블록
        if ($this->PBlock()) {
            $rtv .= "<div class='cs_pages' id='id_page' data-url='{$url}{$this->PBlock()}' data-idx='1'>이전</div>";
        }

        // 현재 블록 페이지 출력
        $pageList = $this->getPage();
        foreach ($pageList as $p) {
            $rtv .= "<div class='cs_pages " . ($page == $p ? 'active' : '') . "' id='id_page' data-url='{$url}{$p}' data-idx='1'>{$p}</div>";
        }

        // 다음 블록
        if ($this->NBlock()) {
            $rtv .= "<div class='cs_pages' id='id_page' data-url='{$url}{$this->NBlock()}' data-idx='1'>다음</div>";
        }
        $rtv .= '</div>';

        return $rtv;
    }
}

class SEARCHTEXT
{
    public $text;
    public $where = '1';

    function __construct($text)
    {
        $this->text = $text;
    }

    function getTextToList($dao, string $searchCol, string $key = ''): string
    {
        $rtv = [];
        $vo = $dao->SELECT("{$searchCol} LIKE '%{$this->text}%'");

        if ($vo) {
            foreach ($vo as $v) {
                if ($key == '') {
                    array_push($rtv, $v->{key($v)});
                } else {
                    array_push($rtv, $v->{$key});
                }
            }
        }

        return implode(',', $rtv);
    }

    function getArrayToList($dao, string $searchCol, string $inList, string $key = ''): string
    {
        $rtvArr = [];
        $vo = $dao->SELECT("{$searchCol} IN ({$inList})");

        if ($vo) {
            foreach ($vo as $v) {
                if ($key == '') {
                    array_push($rtvArr, $v->{key($v)});
                } else {
                    array_push($rtvArr, $v->{$key});
                }
            }

            $rtv = implode(',', $rtvArr);
        } else {
            $rtv = '';
        }

        return $rtv;
    }

    function setWhere(string $inColName, string $list): void
    {
        // 초기값 TRUE에서 WHERE 사용조건으로 변경
        if ($this->where === '1') {
            $this->where = '';
        } else {
            $this->where .= ' OR ';
        }

        // 맨 앞, 뒤에 ','가 들어갈 경우 제외
        if (substr($list, 0, 1) === ',') {
            $list = substr($list, 1, strlen($list) - 1);
        }
        if (substr($list, strlen($list) - 1, 1) === ',') {
            $list = substr($list, 0, strlen($list) - 1);
        }

        // 객체 WHERE절 변경
        $this->where .= "{$inColName} IN ({$list})";
    }

    function isNone(): string
    {
        if ($this->where === '1') {
            $this->where = '0';
        }
        return '0';
    }
}

function getPhoneNum($p)
{
    $rtv = '';

    try {
        $p = str_replace('-', '', $p);
        $p = str_replace(' ', '', $p);
        $p = str_replace('.', '', $p);

        if (!is_numeric($p)) {
            throw new Exception($p);
        }

        switch (strlen($p)) {
            case 9: // 02-123-4567
                $rtv = substr($p, 0, 2) . '-' . substr($p, 2, 3) . '-' . substr($p, 5, 4);
                break;

            case 10: // 016-123-4567
                $rtv = substr($p, 0, 3) . '-' . substr($p, 3, 3) . '-' . substr($p, 6, 4);
                break;

            case 11: // 010-1234-5678
                $rtv = substr($p, 0, 3) . '-' . substr($p, 3, 4) . '-' . substr($p, 7, 4);
                break;

            default:
                // $p == NULL || $p == undefinded
                throw new Exception();
                break;
        }
    } catch (Exception $e) {
        $rtv = $p;
    }

    return $rtv;
}
