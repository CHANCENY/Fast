<?php @session_start();

\FormViewCreation\Logging::signingOut();
\GlobalsFunctions\Globals::redirect(\GlobalsFunctions\Globals::home());
?>