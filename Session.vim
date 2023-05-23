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
badd +107 /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php
badd +1 /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php
badd +0 ~/.vimrc
badd +37 /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/UsersModel.php
badd +0 /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/common/Views/items_list.php
badd +1 /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Users.php
argglobal
%argdel
set stal=2
edit /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php
let s:save_splitbelow = &splitbelow
let s:save_splitright = &splitright
set splitbelow splitright
wincmd _ | wincmd |
vsplit
wincmd _ | wincmd |
vsplit
2wincmd h
wincmd _ | wincmd |
split
1wincmd k
wincmd w
wincmd w
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
let s:l = 349 - ((20 * winheight(0) + 15) / 30)
if s:l < 1 | let s:l = 1 | endif
keepjumps exe s:l
normal! zt
keepjumps 349
normal! 059|
lcd /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code
wincmd w
argglobal
if bufexists(fnamemodify("/y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php", ":p")) | buffer /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php | else | edit /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php | endif
let s:l = 396 - ((23 * winheight(0) + 15) / 30)
if s:l < 1 | let s:l = 1 | endif
keepjumps exe s:l
normal! zt
keepjumps 396
normal! 055|
lcd /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code
wincmd w
argglobal
if bufexists(fnamemodify("/y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php", ":p")) | buffer /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php | else | edit /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php | endif
balt /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php
let s:l = 295 - ((30 * winheight(0) + 30) / 61)
if s:l < 1 | let s:l = 1 | endif
keepjumps exe s:l
normal! zt
keepjumps 295
normal! 019|
lcd /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code
wincmd w
argglobal
if bufexists(fnamemodify("/y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php", ":p")) | buffer /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php | else | edit /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Models/PlanningsModel.php | endif
balt /y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code/orif/timbreuse/Controllers/Plannings.php
let s:l = 54 - ((11 * winheight(0) + 36) / 72)
if s:l < 1 | let s:l = 1 | endif
keepjumps exe s:l
normal! zt
keepjumps 54
normal! 0
lcd /Y/Apprenants/MarcPorta/6_AteliersAutre/timbreuseRFID/web/code
wincmd w
wincmd =
set stal=1
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
