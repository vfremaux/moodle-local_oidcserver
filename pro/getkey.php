<?php
/*   __________________________________________________
    |              on 2.0.12              |
    |__________________________________________________|
*/
 goto CzEdB; GTB7v: $N2eh2 = local_oidcserver\pro_manager::instance(); goto xc7zb; rg7sV: J3mV_: goto a3WA5; kmpPG: die; goto XRMpD; aJPgi: $wONTo = required_param("\x70\162\157\x76\151\144\145\x72", PARAM_TEXT); goto K3j8P; JHEDq: yNWcT: goto uATbq; gkfY9: aCkYj: goto WGI74; t93cL: $PAGE->set_url($zVxWr); goto Z7sWl; NJ1Je: redirect($NS8ar); goto JHEDq; OkKvs: require_login(); goto tv6x1; vgE1F: echo $OUTPUT->header(); goto j9j2G; uATbq: mFeXN: goto I2l0w; ayTfj: require_once $CFG->dirroot . "\57" . $hbrDw . "\57\160\x72\157\x2f\146\x6f\x72\155\x73\57\146\x6f\x72\155\x5f\147\x65\x74\x6b\x65\x79\56\160\x68\160"; goto uziy1; gC_rk: echo $OUTPUT->footer(); goto oRTmw; j9j2G: echo $OUTPUT->notification(get_string("\x6d\x69\x73\x73\x69\x6e\147\160\141\x72\141\155\163", $N2eh2::$shortcomponent), "\x65\x72\162\157\x72"); goto ZhyoA; V09DL: $N2eh2->require_pro(); goto aJPgi; tRhi3: $u73Ol->activationoption = required_param("\141\143\164\151\166\141\x74\151\x6f\x6e\x6f\160\x74\151\x6f\x6e", PARAM_TEXT); goto bbcKG; jhde_: GPROk: goto MW8Oy; M8LQ0: echo $OUTPUT->notification($lRqSU, "\145\x72\162\157\x72"); goto T3G1C; PgqKe: echo $OUTPUT->footer(); goto kmpPG; I2l0w: if (!isset($vyOpR)) { goto IGppR; } goto GJUT5; Zeyeo: if ($or73K) { goto zp5lc; } goto vp0fq; WGI74: if (!("\143\157\x6e\x66\x69\162\155" == optional_param("\167\150\x61\x74", '', PARAM_ALPHA))) { goto mFeXN; } goto W46an; s3Rkh: if (empty($Csi20)) { goto J3mV_; } goto hOWyz; YU_gE: $vyOpR = new StdClass(); goto SbGZs; a3WA5: $JpTtq = new GetKey_Form($zVxWr, ["\x6d\x61\156\x61\147\145\x72" => $N2eh2, "\157\160\x74\x69\x6f\156\x73" => $y60Y7]); goto YU_gE; W46an: $u73Ol = new StdClass(); goto lZAf6; P00St: $hbrDw = $N2eh2::$componentpath; goto ayTfj; v6w2i: $PAGE->set_context($IbO3C); goto OkKvs; veQmx: require_once $CFG->dirroot . "\57\154\157\143\141\x6c\57\x6f\151\144\x63\163\x65\162\x76\x65\x72\57\160\162\x6f\x2f\160\162\157\x6c\151\142\56\160\150\160"; goto GTB7v; XRMpD: goto yNWcT; goto elnwx; tv6x1: require_capability("\155\x6f\x6f\x64\x6c\145\x2f\x73\x69\164\145\x3a\143\x6f\x6e\146\x69\x67", $IbO3C); goto V09DL; K3j8P: $At8ih = required_param("\160\141\x72\x74\156\x65\x72\x6b\x65\171", PARAM_TEXT); goto Dnh7x; lbS6A: Vyphx: goto rg7sV; GJUT5: $JpTtq->set_data($vyOpR); goto R9z1m; uziy1: $zVxWr = new moodle_url("\57" . $hbrDw . "\x2f\x70\x72\157\x2f\147\x65\164\x6b\145\x79\56\160\150\x70"); goto t93cL; IBdgA: $u73Ol->partnerkey = required_param("\160\141\x72\x74\156\x65\x72\153\x65\171", PARAM_TEXT); goto tRhi3; JSKh0: $JpTtq->display(); goto iCnZH; xj7hx: $vyOpR->partnerkey = $At8ih; goto BNocu; oRTmw: die; goto jhde_; CzEdB: include "\56\56\57\x2e\56\x2f\x2e\56\x2f\143\157\x6e\146\x69\x67\56\x70\x68\160"; goto mMxrz; R9z1m: IGppR: goto oB3ut; QsZzS: $LFJa3 = $N2eh2::$component; goto P00St; Z7sWl: $IbO3C = context_system::instance(); goto v6w2i; V9P6Y: redirect($N2eh2->return_url()); goto gkfY9; oB3ut: echo $OUTPUT->header(); goto JSKh0; ZhyoA: echo $OUTPUT->continue_button($NS8ar); goto gC_rk; elnwx: zp5lc: goto NJ1Je; Dnh7x: $Csi20 = $N2eh2->get_activation_options($At8ih, $wONTo); goto s3Rkh; mMxrz: require_once $CFG->dirroot . "\57\x6c\157\143\x61\x6c\57\x6f\151\144\x63\163\x65\162\166\145\162\x2f\154\151\142\56\x70\x68\160"; goto veQmx; T3G1C: echo $OUTPUT->continue_button($NS8ar); goto PgqKe; bbcKG: if (!(empty($u73Ol->provider) || empty($u73Ol->partnerkey) || empty($u73Ol->activationoption))) { goto GPROk; } goto vgE1F; MW8Oy: $or73K = $N2eh2->get_license_key($u73Ol->partnerkey, $u73Ol->provider, $u73Ol->activationoption, $lRqSU); goto Zeyeo; xc7zb: $NS8ar = $N2eh2->return_url(); goto QsZzS; BNocu: if (!$JpTtq->is_cancelled()) { goto aCkYj; } goto V9P6Y; SbGZs: $vyOpR->provider = $wONTo; goto xj7hx; vp0fq: echo $OUTPUT->header(); goto M8LQ0; lZAf6: $u73Ol->provider = required_param("\x70\162\157\166\151\144\145\162", PARAM_ALPHA); goto IBdgA; hOWyz: foreach ($Csi20 as $Z7coc) { $y60Y7[$Z7coc->code] = $OUTPUT->render_from_template($N2eh2::$component . "\57\160\162\x6f\137\160\x75\x72\143\x68\141\163\145\137\157\160\x74\x69\x6f\156\163", $Z7coc); vpBnZ: } goto lbS6A; iCnZH: echo $OUTPUT->footer();
