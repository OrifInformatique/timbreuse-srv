let SessionLoad = 1
if &cp | set nocp | endif
let s:so_save = &g:so | let s:siso_save = &g:siso | setg so=0 siso=0 | setl so=-1 siso=-1
let v:this_session=expand("<sfile>:p")
silent only
if expand('%') == '' && !&modified && line('$') <= 1 && getline(1) == ''
  let s:wipebuf = bufnr('%')
endif
let s:shortmess_save = &shortmess
if &shortmess =~ 'A'
  set shortmess=aoOA
else
  set shortmess=aoO
endif
badd +0 /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php
badd +0 /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php
argglobal
%argdel
edit /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php
let s:save_splitbelow = &splitbelow
let s:save_splitright = &splitright
set splitbelow splitright
wincmd _ | wincmd |
vsplit
1wincmd h
wincmd w
let &splitbelow = s:save_splitbelow
let &splitright = s:save_splitright
wincmd t
let s:save_winminheight = &winminheight
let s:save_winminwidth = &winminwidth
set winminheight=0
set winheight=1
set winminwidth=0
set winwidth=1
wincmd =
argglobal
let s:l = 107 - ((35 * winheight(0) + 36) / 73)
if s:l < 1 | let s:l = 1 | endif
keepjumps exe s:l
normal! zt
keepjumps 107
normal! 0
lcd /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code
wincmd w
argglobal
if bufexists(fnamemodify("/y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php", ":p")) | buffer /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php | else | edit /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php | endif
let s:l = 274 - ((33 * winheight(0) + 36) / 73)
if s:l < 1 | let s:l = 1 | endif
keepjumps exe s:l
normal! zt
keepjumps 274
normal! 017|
lcd /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code
wincmd w
2wincmd w
wincmd =
if exists('s:wipebuf') && len(win_findbuf(s:wipebuf)) == 0
  silent exe 'bwipe ' . s:wipebuf
endif
unlet! s:wipebuf
set winheight=1 winwidth=20
let &shortmess = s:shortmess_save
let &winminheight = s:save_winminheight
let &winminwidth = s:save_winminwidth
let s:sx = expand("<sfile>:p:r")."x.vim"
if filereadable(s:sx)
  exe "source " . fnameescape(s:sx)
endif
let &g:so = s:so_save | let &g:siso = s:siso_save
doautoall SessionLoadPost
unlet SessionLoad
" vim: set ft=vim :
