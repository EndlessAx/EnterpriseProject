<html>
<head>
<link rel="stylesheet" type="text/css" href="/EnterpriseProject/WebContent/view/css/main.css">
</head>
<div class="bar">
<div class="header">
<ul>
<li><a class="c"  href="http://cgi.cse.unsw.edu.au/~attr192/ass2/unswmate.cgi">UNSW-Mate</a></li>
<li><form method="GET" action="http://cgi.cse.unsw.edu.au/~attr192/ass2/unswmate.cgi">
<input type="hidden" name="action" value="search">
<input class="search" type="text" name="search" value="Search for username" onfocus="if(this.value == 'Search for username'){this.value = '';}" onblur="if(this.value == ''){this.value='Search for username';}">
<input class="searchb" type="image" src="/EnterpriseProject/search.png" alt="Submit" width="20" height="20">
</form></li>
<li style="float:right">
<a class="c" href="http://cgi.cse.unsw.edu.au/~attr192/ass2/unswmate.cgi?page=login"><small>Have an account? <b>Sign in</b></small></a>
or<a class="c" href="http://cgi.cse.unsw.edu.au/~attr192/ass2/unswmate.cgi?action=sign_up"><small><b>Sign up!</b></small></a>

</li>
</ul>
</div>
</div>
<br><br><br><br><br><br><br><br><br><br>
<form method="get" action="/search" id="search">
  <input name="q" type="text" size="40" placeholder="Search..." />
</form>
			<input class="mainSearchbox" id='searchbox' type="text" name="search_text" size="80" maxlength="40" x-webkit-speech/>

<div class="footer" style="clear:both;color:#B8B8B8 ;text-align:center;">
<small>Copyright © UNSW-Mate ahsjkdhajshdkahsdjhak</small></div>
<form method="get" action="http://www.google.com/search">
 <input type="text" name="q" size="30" x-webkit-speech/>
 <input type="submit" value="Google Search" />
</form>
</html>