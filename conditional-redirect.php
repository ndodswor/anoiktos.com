<?php
//conditional redirect generator
//This page exists as a quick way for users to generate the conditional redirect
//made because not everyone I work with is familiar with the way function calls work
$pageTitle    = "Conditional Redirect";
$pageSubTitle = "(Conditional Redirect)";
include "header.php";
include "main-nav.php";
include "functions.php";
?>
<section id="form">
  <form action="conditional-redirect.php" method="post">
   Mobile URL:
    <input type="text" name="mobileURL">
    <br>
Redirect to:
<select name="home_only">
			<option value="true">Home Page</option>
			<option value="false">Same Page</option>
		</select>
<br>
String to check for (separate multiple strings with commas):
<input type="text" name="urlToTest">
<br>
Redirect sites which:
<select name="isExclusive">
			<option value="true">Contain the above string</option>
			<option value="false">Don't contain the above string</option>
		</select>
    <input type="hidden" name="form_submitted" value="true">
    <br>
    <input type="submit" value="Request Script">
  </form>
</section>

<?php
if ($_POST["form_submitted"]) {
print "<section id=\"codeSection\">";
// separate urlToTest 
$urlToTest = $_POST["urlToTest"];
// get POST variables 
$homeToggle = $_POST["home_only"];
$isExclusiveToggle = $_POST["isExclusive"];
$urlToTest = preg_replace('/\s+/', '', $urlToTest);
if (strpos($urlToTest,',') !== false) {
 $urlToTest = preg_replace('/,/', "\",\"", $urlToTest);
 $urlToTest = "[\"" . $urlToTest . "\"]";
}
else
{
$urlToTest = "\"" . $urlToTest . "\"";
}

//now print out the script in a textarea (for easy copy/paste)
?>
<p>Add the following code to the &lt;head&gt; section of your website:</p>
<textarea rows="30" cols="50">
	<script type="text/javascript">
	<?php print htmlspecialchars(file_get_contents("conditional-redirect.js")); ?>
	</script>
	<script type="text/javascript">
	<?php print "DM_redirect_conditional(\"" . $_POST["mobileURL"] . "\", " . $homeToggle . ", " . $urlToTest . ", " . $isExclusiveToggle . ");"; ?>
	</script>
</textarea>

<?php
print "</section id=\"codeSection\">";
}
?>
<?php
include "footer.php";
?>	