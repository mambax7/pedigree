<?php
// -------------------------------------------------------------------------

require_once dirname(dirname(__DIR__)) . '/mainfile.php';

$moduleDirName = basename(__DIR__);
xoops_loadLanguage('main', $moduleDirName);

// Include any common code for this module.
require_once XOOPS_ROOT_PATH . "/modules/{$moduleDirName}/include/common.php";

// Get all HTTP post or get parameters into global variables that are prefixed with "param_"
//import_request_variables("gp", "param_");
//extract($_GET, EXTR_PREFIX_ALL, 'param');
//extract($_POST, EXTR_PREFIX_ALL, 'param');

// This page uses smarty templates. Set "$xoopsOption['template_main']" before including header
$xoopsOption['template_main'] = 'pedigree_pedigree.tpl';

include $GLOBALS['xoops']->path('/header.php');

//always start with Anika
$pedid = XoopsRequest::getInt('pedid', 3);
/*
if (!$pedid) {
    $pedid = '3';
}
*/
//draw pedigree

$detail = XoopsRequest::getString('detail', '', 'POST');
$detail = trim($detail);

$queryString = '
SELECT d.Id as d_id,
d.NAAM as d_naam,
d.Id_owner as d_id_owner,
d.Id_breeder as d_id_breeder,
d.roft as d_roft,
d.kleur as d_kleur,
d.mother as d_mother,
d.father as d_father,
d.geboortedatum as d_geboortedatum,
d.overleden as d_overleden,
d.boek as d_boek,
d.nhsb as d_nhsb,
d.foto as d_foto,
d.overig as d_overig,
d.hd as d_hd,
f.Id as f_id,
f.NAAM as f_naam,
f.mother as f_mother,
f.father as f_father,
f.foto as f_foto,
f.hd as f_hd,
m.Id as m_id,
m.NAAM as m_naam,
m.mother as m_mother,
m.father as m_father,
m.foto as m_foto,
m.hd as m_hd,
ff.Id as ff_id,
ff.NAAM as ff_naam,
ff.roft as ff_roft,
ff.mother as ff_mother,
ff.father as ff_father,
ff.foto as ff_foto,
ff.hd as ff_hd,
mf.Id as mf_id,
mf.NAAM as mf_naam,
mf.mother as mf_mother,
mf.father as mf_father,
mf.nhsb as mf_nhsb,
mf.foto as mf_foto,
mf.hd as mf_hd,
fm.Id as fm_id,
fm.NAAM as fm_naam,
fm.mother as fm_mother,
fm.father as fm_father,
fm.nhsb as fm_nhsb,
fm.foto as fm_foto,
fm.hd as fm_hd,
mm.Id as mm_id,
mm.NAAM as mm_naam,
mm.kleur as mm_kleur,
mm.mother as mm_mother,
mm.father as mm_father,
mm.nhsb as mm_nhsb,
mm.foto as mm_foto,
mm.hd as mm_hd,
fff.Id as fff_id,
fff.NAAM as fff_naam,
fff.kleur as fff_kleur,
fff.nhsb as fff_nhsb,
fff.foto as fff_foto,
fff.hd as fff_hd,
ffm.Id as ffm_id,
ffm.NAAM as ffm_naam,
ffm.kleur as ffm_kleur,
ffm.nhsb as ffm_nhsb,
ffm.foto as ffm_foto,
ffm.hd as ffm_hd,
fmf.Id as fmf_id,
fmf.NAAM as fmf_naam,
fmf.kleur as fmf_kleur,
fmf.nhsb as fmf_nhsb,
fmf.foto as fmf_foto,
fmf.hd as fmf_hd,
fmm.Id as fmm_id,
fmm.NAAM as fmm_naam,
fmm.kleur as fmm_kleur,
fmm.nhsb as fmm_nhsb,
fmm.foto as fmm_foto,
fmm.hd as fmm_hd,
mmf.Id as mmf_id,
mmf.NAAM as mmf_naam,
mmf.kleur as mmf_kleur,
mmf.nhsb as mmf_nhsb,
mmf.foto as mmf_foto,
mmf.hd as mmf_hd,
mff.Id as mff_id,
mff.NAAM as mff_naam,
mff.kleur as mff_kleur,
mff.nhsb as mff_nhsb,
mff.foto as mff_foto,
mff.hd as mff_hd,
mfm.Id as mfm_id,
mfm.NAAM as mfm_naam,
mfm.kleur as mfm_kleur,
mfm.nhsb as mfm_nhsb,
mfm.foto as mfm_foto,
mfm.hd as mfm_hd,
mmm.Id as mmm_id,
mmm.NAAM as mmm_naam,
mmm.kleur as mmm_kleur,
mmm.nhsb as mmm_nhsb,
mmm.foto as mmm_foto,
mmm.hd as mmm_hd
FROM ' . $GLOBALS['xoopsDB']->prefix('pedigree_tree') . " d
LEFT JOIN xoops_pedigree f ON d.father = f.Id
LEFT JOIN xoops_pedigree m ON d.mother = m.Id
LEFT JOIN xoops_pedigree ff ON f.father = ff.Id
LEFT JOIN xoops_pedigree fff ON ff.father = fff.Id
LEFT JOIN xoops_pedigree ffm ON ff.mother = ffm.Id
LEFT JOIN xoops_pedigree mf ON m.father = mf.Id
LEFT JOIN xoops_pedigree mff ON mf.father = mff.Id
LEFT JOIN xoops_pedigree mfm ON mf.mother = mfm.Id
LEFT JOIN xoops_pedigree fm ON f.mother = fm.Id
LEFT JOIN xoops_pedigree fmf ON fm.father = fmf.Id
LEFT JOIN xoops_pedigree fmm ON fm.mother = fmm.Id
LEFT JOIN xoops_pedigree mm ON m.mother = mm.Id
LEFT JOIN xoops_pedigree mmf ON mm.father = mmf.Id
LEFT JOIN xoops_pedigree mmm ON mm.mother = mmm.Id
where d.Id={$pedid}";

$result = $GLOBALS['xoopsDB']->query($queryString);
/*
//get module configuration
$moduleHandler = xoops_getHandler('module');
$module        = $moduleHandler->getByDirname('pedigree');
$configHandler = xoops_getHandler('config');
$moduleConfig  = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
*/
$pic = $pedigree->getConfig('pics');
$hd  = $pedigree->getConfig('hd');
while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
    //create array for dog (and all parents)
    //selected dog
    $d['d']['name']   = stripslashes($row['d_naam']);
    $d['d']['id']     = $row['d_id'];
    $d['d']['roft']   = $row['d_roft'];
    $d['d']['nhsb']   = $row['d_nhsb'];
    $d['d']['colour'] = $row['d_kleur'];
    if ($pic == 1) {
        $d['d']['photo'] = $row['d_foto'];
    }
    if ($hd == 1) {
        $d['d']['hd'] = hd($row['d_hd']);
    }
    //father
    $d['f']['name'] = stripslashes($row['f_naam']);
    $d['f']['id']   = $row['f_id'];
    if ($pic == 1) {
        $d['f']['photo'] = $row['f_foto'];
    }
    if ($hd == 1) {
        $d['f']['hd'] = hd($row['f_hd']);
    }
    //mother
    $d['m']['name'] = stripslashes($row['m_naam']);
    $d['m']['id']   = $row['m_id'];
    if ($pic == 1) {
        $d['m']['photo'] = $row['m_foto'];
    }
    if ($hd == 1) {
        $d['m']['hd'] = hd($row['m_hd']);
    }
    //grandparents
    //father father
    $d['ff']['name'] = stripslashes($row['ff_naam']);
    $d['ff']['id']   = $row['ff_id'];
    if ($pic == 1) {
        $d['ff']['photo'] = $row['ff_foto'];
    }
    if ($hd == 1) {
        $d['ff']['hd'] = hd($row['ff_hd']);
    }
    //father mother
    $d['fm']['name'] = stripslashes($row['fm_naam']);
    $d['fm']['id']   = $row['fm_id'];
    if ($pic == 1) {
        $d['fm']['photo'] = $row['fm_foto'];
    }
    if ($hd == 1) {
        $d['fm']['hd'] = hd($row['fm_hd']);
    }
    //mother father
    $d['mf']['name'] = stripslashes($row['mf_naam']);
    $d['mf']['id']   = $row['mf_id'];
    if ($pic == 1) {
        $d['mf']['photo'] = $row['mf_foto'];
    }
    if ($hd == 1) {
        $d['mf']['hd'] = hd($row['mf_hd']);
    }
    //mother mother
    $d['mm']['name'] = stripslashes($row['mm_naam']);
    $d['mm']['id']   = $row['mm_id'];
    if ($pic == 1) {
        $d['mm']['photo'] = $row['mm_foto'];
    }
    if ($hd == 1) {
        $d['mm']['hd'] = hd($row['mm_hd']);
    }
    //great-grandparents
    //father father father
    $d['fff']['name'] = stripslashes($row['fff_naam']);
    $d['fff']['id']   = $row['fff_id'];
    if ($pic == 1) {
        $d['fff']['photo'] = $row['fff_foto'];
    }
    if ($hd == 1) {
        $d['fff']['hd'] = hd($row['fff_hd']);
    }
    //father father mother
    $d['ffm']['name'] = stripslashes($row['ffm_naam']);
    $d['ffm']['id']   = $row['ffm_id'];
    if ($pic == 1) {
        $d['ffm']['photo'] = $row['ffm_foto'];
    }
    if ($hd == 1) {
        $d['ffm']['hd'] = hd($row['ffm_hd']);
    }
    //father mother father
    $d['fmf']['name'] = stripslashes($row['fmf_naam']);
    $d['fmf']['id']   = $row['fmf_id'];
    if ($pic == 1) {
        $d['fmf']['photo'] = $row['fmf_foto'];
    }
    if ($hd == 1) {
        $d['fmf']['hd'] = hd($row['fmf_hd']);
    }
    //father mother mother
    $d['fmm']['name'] = stripslashes($row['fmm_naam']);
    $d['fmm']['id']   = $row['fmm_id'];
    if ($pic == 1) {
        $d['fmm']['photo'] = $row['fmm_foto'];
    }
    if ($hd == 1) {
        $d['fmm']['hd'] = hd($row['fmm_hd']);
    }
    //mother father father
    $d['mff']['name'] = stripslashes($row['mff_naam']);
    $d['mff']['id']   = $row['mff_id'];
    if ($pic == 1) {
        $d['mff']['photo'] = $row['mff_foto'];
    }
    if ($hd == 1) {
        $d['mff']['hd'] = hd($row['mff_hd']);
    }
    //mother father mother
    $d['mfm']['name'] = stripslashes($row['mfm_naam']);
    $d['mfm']['id']   = $row['mfm_id'];
    if ($pic == 1) {
        $d['mfm']['photo'] = $row['mfm_foto'];
    }
    if ($hd == 1) {
        $d['mfm']['hd'] = hd($row['mfm_hd']);
    }
    //mother mother father
    $d['mmf']['name'] = stripslashes($row['mmf_naam']);
    $d['mmf']['id']   = $row['mmf_id'];
    if ($pic == 1) {
        $d['mmf']['photo'] = $row['mmf_foto'];
    }
    if ($hd == 1) {
        $d['mmf']['hd'] = hd($row['mmf_hd']);
    }
    //mother mother mother
    $d['mmm']['name'] = stripslashes($row['mmm_naam']);
    $d['mmm']['id']   = $row['mmm_id'];
    if ($pic == 1) {
        $d['mmm']['photo'] = $row['mmm_foto'];
    }
    if ($hd == 1) {
        $d['mmm']['hd'] = hd($row['mmm_hd']);
    }
}

//add data to smarty template
$xoopsTpl->assign('page_title', stripslashes($row['d_naam']));
//assign dog
$xoopsTpl->assign('d', $d);
//assign config options
$xoopsTpl->assign('overview', $pedigree->getConfig('overview'));
$sign = $pedigree->getConfig('gender');
if (1 == $sign) {
    $xoopsTpl->assign('male', "<img src=\"assets/images/male.gif\" alt=\"" . _MA_PEDIGREE_MALE . "\">");
    $xoopsTpl->assign('female', "<img src=\"assets/images/female.gif\" alt=\"" . _MA_PEDIGREE_FEMALE . "\">");
}
$addit = $pedigree->getConfig('adinfo');
if (1 == $addit) {
    $xoopsTpl->assign('addinfo', '1');
}
$xoopsTpl->assign('pics', $pic);
//assign extra display options
$xoopsTpl->assign('unknown', 'Unknown');
$xoopsTpl->assign('SD', _MA_PEDIGREE_SD);
$xoopsTpl->assign('PA', _MA_PEDIGREE_PA);
$xoopsTpl->assign('GP', _MA_PEDIGREE_GP);
$xoopsTpl->assign('GGP', _MA_PEDIGREE_GGP);

//comments and footer
include XOOPS_ROOT_PATH . '/footer.php';

