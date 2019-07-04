<?php


/***********************************************Home*****************************************************/
// Home
Breadcrumbs::register('home', function($breadcrumbs)
{
    $breadcrumbs->push('Home', route('home'));
});

/************************************************Home > Conf*****************************************************/

// Home > confAcronymEdition
Breadcrumbs::register('homeConf', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
});

// Home > confAcronymEdition > edit
Breadcrumbs::register('editConf', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit conference');

});

// Home > confAcronymEdition > editSubmission
Breadcrumbs::register('editSub', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit submission');

});

/************************************************Home > Conf > MSG *****************************************************/

// Home > confAcronymEdition > MsgTemp
Breadcrumbs::register('msgTemp', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Messages templates');

});

// Home > confAcronymEdition > MsgTemp > create
Breadcrumbs::register('crMsgTemp', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Messages templates',route('conferences.messages.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Add message');

});

// Home > confAcronymEdition > MsgTemp > edit
Breadcrumbs::register('editMsgTemp', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Messages templates',route('conferences.messages.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit message');

});

/************************************************Home > Conf > Topics*****************************************************/


// Home > confAcronymEdition > Topics
Breadcrumbs::register('topics', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Topics');

});

// Home > confAcronymEdition > Topics > create
Breadcrumbs::register('crTopic', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Topics',route('conferences.topics.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Add topic');

});

// Home > confAcronymEdition > Topics > edit
Breadcrumbs::register('editTopic', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Topics',route('conferences.topics.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit topic');

});

/************************************************Home > Conf > Papaer Questions*****************************************************/

// Home > confAcronymEdition > pquestions
Breadcrumbs::register('pquestions', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Paper questions');

});

// Home > confAcronymEdition > pquestions > create
Breadcrumbs::register('crPq', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Paper questions',route('conferences.pquestions.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Add paper question');

});

// Home > confAcronymEdition > pquestions > edit
Breadcrumbs::register('editPq', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Paper questions',route('conferences.pquestions.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit paper question');

});

/************************************************Home > Conf > Review Questions*****************************************************/

// Home > confAcronymEdition > rquestions
Breadcrumbs::register('rquestions', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Review questions');

});

// Home > confAcronymEdition > rquestions > create
Breadcrumbs::register('crRq', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Review questions',route('conferences.rquestions.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Add review question');

});

// Home > confAcronymEdition > rquestions > edit
Breadcrumbs::register('editRq', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Review questions',route('conferences.pquestions.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit review question');

});

/************************************************Home > Conf > Criterias*****************************************************/

// Home > confAcronymEdition > criterias
Breadcrumbs::register('criterias', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Criterias');

});

// Home > confAcronymEdition > criterias > create
Breadcrumbs::register('crCr', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Criterias',route('conferences.criterias.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Add criteria');

});

// Home > confAcronymEdition > criterias > edit
Breadcrumbs::register('editCr', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Criterias',route('conferences.criterias.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit criteria');

});

/************************************************Home > Conf > Pstatus*****************************************************/

// Home > confAcronymEdition > Pstatus
Breadcrumbs::register('pstatus', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Paper statuses');

});

// Home > confAcronymEdition > Pstatus > create
Breadcrumbs::register('crPs', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Paper statuses',route('conferences.paperstatus.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Add paper status');

});

// Home > confAcronymEdition > Pstatus > edit
Breadcrumbs::register('editPs', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Paper statuses',route('conferences.paperstatus.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit paper status');

});




/************************************************Home > Conf > sub_papers*****************************************************/

// Home > confAcronymEdition > sub_papers
Breadcrumbs::register('sub_papers', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Submissions', route('conferences.papers.index',[$conference->confAcronym,$conference->confEdition]));
});

// Home > confAcronymEdition > sub_papers
Breadcrumbs::register('assignStatus', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Assign status to paper');
});

// Home > confAcronymEdition > submitPaper
Breadcrumbs::register('submitPaper', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('Submit paper');
});

// Home > confAcronymEdition > submitPaperCr
Breadcrumbs::register('submitPaperCr', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition/*, route('conferences.show',[$conference->confAcronym,$conference->confEdition])*/);
    $breadcrumbs->push('Submit paper camera ready');
});

//Home > confAcronymEdition > sub_papers > paper $id

Breadcrumbs::register('paper', function($breadcrumbs,$conference, $paper ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Submissions', route('conferences.papers.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('paper '.$paper->id, route('conferences.papers.show', [$conference->confAcronym,$conference->confEdition,$paper->id]));
});


//Home > confAcronymEdition > sub_papers > paper $id

Breadcrumbs::register('paperAssign', function($breadcrumbs,$conference, $paper ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Submissions', route('conferences.papers.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('assign reviewers to paper '.$paper->id, route('conferences.papers.show', [$conference->confAcronym,$conference->confEdition,$paper->id]));
});

//Home > confAcronymEdition  > sub_papers > paper with keywords

Breadcrumbs::register('paperKeys', function($breadcrumbs,$conference,$key ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));    
    $breadcrumbs->push('Submissions', route('conferences.papers.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Papers have keywords '.$key);
});

/************************************************Home > Conf > Authors*****************************************************/


//Home > confAcronymEdition  > Authors
Breadcrumbs::register('authors', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Authors', route('conferences.authors.index',[$conference->confAcronym,$conference->confEdition]));
});

//Home > confAcronymEdition > Authors > paers of
Breadcrumbs::register('papersOf', function($breadcrumbs,$conference,$author)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Authors', route('conferences.authors.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Papers of '.$author->first_name .' ' . $author->last_name);
});

//Home > confAcronymEdition > Authors > paers of
Breadcrumbs::register('comite', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Committee', route('conferences.comite.index',[$conference->confAcronym,$conference->confEdition]));

});

//Home > confAcronymEdition > sub_papers > paper $id

Breadcrumbs::register('revAssign', function($breadcrumbs,$conference, $rev ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Committee', route('conferences.comite.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('assign papers to reviewer '.$rev->first_name.' '.$rev->last_name);
});

/************************************************Home > Conf > Program*****************************************************/

//Home > confAcronymEdition > Program 
Breadcrumbs::register('program', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Program');

});

//Home > confAcronymEdition > Program > Preview
Breadcrumbs::register('preview', function($breadcrumbs,$conference)
{
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition, route('conferences.show',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Program', route('conferences.slots.index',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Preview');

});

/***********************************Reviewer papers*************Home > Conf > my papers *****************************************************/
//Home > Conf > my papers 

Breadcrumbs::register('myPapersRev', function($breadcrumbs,$conference ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('My papers', route('conferences.comite.mypapers',[$conference->confAcronym,$conference->confEdition]));
});

//Home > Conf > my papers > review paper $id

Breadcrumbs::register('paperRev', function($breadcrumbs,$conference, $paper ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('My papers', route('conferences.comite.mypapers',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Review paper'.$paper->id);
});

//Home > Conf > my papers > show paper $id

Breadcrumbs::register('paperShowRev', function($breadcrumbs,$conference, $paper ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('My papers', route('conferences.comite.mypapers',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Show paper'.$paper->id);
});


/***********************************Author papers*************Home > Conf > my papers *****************************************************/
//Home > Conf > my papers
Breadcrumbs::register('myPapersAut', function($breadcrumbs,$conference ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('My papers', route('conferences.authors.mypapers',[$conference->confAcronym,$conference->confEdition]));
});

//Home > Conf > my papers > show paper $id

Breadcrumbs::register('paperEditAut', function($breadcrumbs,$conference, $paper ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('My papers', route('conferences.authors.mypapers',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Edit paper'.$paper->id);
});

//Home > Conf > my papers > edit paper $id
Breadcrumbs::register('paperShowAut', function($breadcrumbs,$conference, $paper ) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($conference->confAcronym.$conference->confEdition);
    $breadcrumbs->push('My papers', route('conferences.authors.mypapers',[$conference->confAcronym,$conference->confEdition]));
    $breadcrumbs->push('Show paper'.$paper->id);
});
