<?php
include 'Dbh.php' ; 




//********************************************************************// 
//*******************Class********************************************// 
//********************************************************************// 
class cViewthread extends Dbh { 

	private $Number ;
	
//**********************FUNCTION***************************************//
	protected function GetPosts($Number) { 
	//************** Recursive SQL return *************************** //    
	/* tried testing with recursive SQL 
	$sql = " with recursive cte (id, User, CommentText, IDOfThread, ParentId, level) as (
     select     id,
                User,
                CommentText,
                IDOfThread,
                 ParentId,
                 level
      from       Posts

      where      IDOfThread = 332
       union all
      select     p.id,
                 p.User,
                 p.CommentText,
                 p.IDOfThread,
                p.ParentId,
               p.level
      from       Posts p
      inner join cte
              on p.ParentId = cte.id
    )
    select * from cte" ; 
   
	*/
	//************** End sql BACKUP COMMAND**************************************** // 
	
		$sql = "select Posts.*, Users.avatar from Posts join Users on Posts.User =Users.User where IdOfThread = $Number";
	
	$result = $this->connect()->query($sql);

		while($row = $result->fetch_assoc()) { 
		$data[] = $row ; 
		} 
		//array_reverse($data);
		return $data ; 
	
	
	}// End function 
	
}

//********************************************************************// 
//*******************End CLASS****************************************// 
//********************************************************************// 

//********************************************************************// 
//*******************Class********************************************// 
//********************************************************************// 

class cViewthread_DisplayComments extends cViewthread { 

	public $Number ; 



//**********************FUNCTION***************************************//
	public function ShowComments($Number) { 
		$datas = $this->GetPosts($Number) ;
		echo "type equals: " . gettype($datas) . "<P>";
		foreach ($datas as $data)
		{ 
	  //May need this later to output pictures
    //     $imageURL = 'upload/'.rawurlencode($row["filename"]);
     $CommentText= nl2br($data['CommentText']);
	 $avatarFilePath = $data['avatar'];
	 $id = $data['IDOfThread'];
	 $PostID = $data['id'] ; 
	 $ParentId = $data['ParentId']; 
    convertYoutube($CommentText);
    
   $oFlairs = new cFlairs();
	$oFlairs->DisplayFlairs($CommentText); 
    
    //Work out Margin for comment replies 
   $levelNumber = $data['level'];
	$Level = $data['level'] * 75; // Used to multiply the margin to create nested comments  
	 //$Level = 1 * 75 ;  
	 $margin = 	"<div class='divTableCell' style='margin-left: $Level" . "px ; '/>"; //input the margin in child comments
	 //$margin = 	"<div class='divTableCell' style='margin-left: 75" . "px ; '>"; //input the margin in child comments
 
    
    $ParentComment[] = "";
//Get parent comments into an array 
    if (empty($data['ParentId'])) {
           $ParentComments[$PostID] = "  <div class='divTabledata'>
	<div class='divTableCell'>
		<div class ='UserAndAvatarCommentBox'>
<div > <img src=$avatarFilePath alt='' />	</div> 
	<div class='profileUsername'> {$data['User']} </div> 
</div>
		<div class='pointsincommentbox'> {$data['Upvotes']}points</div>

		<div class='divTableComment'> 
		 $CommentText <br>
		 <div> 
			<div class='divCommentLinks'> 
		 <button type='button'> ⬆</button> 
		<button type='button'> ⬇</button> 
		<div> $PostID </div> 
		<button type='button'> view comment </button> 
		<button type='button'>report </button> 
		<button type='button'>permalink</button> 
		<button type='button' class ='CommentChildButton'>reply</button>
		<div class ='OpenChildCommentBox'> 
		
				<form action='CommentUpload.php' method='post' enctype='multipart/form-data'>
				<table>
				<tr>
				<td></td>
				</tr>
				<input type='text' value=$PostID name='PostId' />
				<input type='text' value='1' name='level' />
				<tr>
				<td>Comment: </td>
				<td> <textarea name='CommentText' cols='100' datas='10' > Enter your posts... 
				</textarea>
				 </td>
				<td></td>
				</tr>
				<tr>
				<td></td>
				<td><input type='submit' name='submit' value='Submit'/></td>
				<td></td>
				</tr>
				</table> 
				</form>
		
		  	
		</div> 
		</div> 
	</div>

 </div>

</div>
</div> 
    \n";
    }
   //Get child comments into an array level 1
       if ($data['ParentId'] && $data['level'] == 1 ) {
           $replies[$ParentId] = "  <div class='divTabledata'>
		<div class='divTableCell' style='margin-left:75px'>
		<div class ='UserAndAvatarCommentBox'>
<div > <img src=$avatarFilePath alt='' />	</div> 
	<div class='profileUsername'> {$data['User']} </div> 
</div>
		<div class='pointsincommentbox'> {$data['Upvotes']}points</div>

		<div class='divTableComment'> 
		 $CommentText <br>
			<div class='divCommentLinks'> 
		 <button type='button'> ⬆</button> 
		<button type='button'> ⬇</button> 
		<div> $PostID </div> 
		<button type='button'> view comment </button> 
		<button type='button'>report </button> 
		<button type='button'>permalink</button> 
		<button type='button' class ='CommentChildButton'>reply</button>
		<div class ='OpenChildCommentBox'> 
		
				<form action='CommentUpload.php' method='post' enctype='multipart/form-data'>
				<table>
				<tr>
				<td></td>
				</tr>
				<input type='text' value=$PostID name='PostId' />
				<input type='text' value={$data['level']} name='level' />
				<tr>
				<td>Comment: </td>
				<td> <textarea name='CommentText' cols='100' datas='10' > Enter your posts... 
				</textarea>
				 </td>
				<td></td>
				</tr>
				<tr>
				<td></td>
				<td><input type='submit' name='submit' value='Submit'/></td>
				<td></td>
				</tr>
				</table> 
				</form>
		
		
		 	
		</div> 
	</div>

 </div>
</div>
</div>
</div> 
    \n";
    }
  
//Get child comments into an array level 2
       if ($data['ParentId'] && $data['level'] == 2 ) {
           $Level2[$ParentId] = " 
		$margin
			<div class ='UserAndAvatarCommentBox'>
<div > <img src=$avatarFilePath alt='' />	</div> 
	<div class='profileUsername'> {$data['User']} </div> 
</div>
		<div class='pointsincommentbox'> {$data['Upvotes']}points</div>

		<div class='divTableComment'> 
		 $CommentText <br>
			<div class='divCommentLinks'> 
		 <button type='button'> ⬆</button> 
		<button type='button'> ⬇</button> 
		<div> $PostID </div> 
		<button type='button'> view comment </button> 
		<button type='button'>report </button> 
		<button type='button'>permalink</button> 
		<button type='button' class ='CommentChildButton'>reply</button>
		<div class ='OpenChildCommentBox'> 
		
				<form action='ChildCommentUpload.php' method='post' enctype='multipart/form-data'>
				<table>
				<tr>
				<td></td>
				</tr>
				<input type='text' value=$PostID name='PostId' />
				<input type='text' value={$data['level']} name='Level' />
				<tr>
				<td>Comment: </td>
				<td> <textarea name='CommentText' cols='100' datas='10' > Enter your posts... 
				</textarea>
				 </td>
				<td></td>
				</tr>
				<tr>
				<td></td>
				<td><input type='submit' name='submit' value='Submit'/></td>
				<td></td>
				</tr>
				</table> 
				</form>
		
		
		 	
		</div> 
	</div>

 </div>
</div>
</div>
</div> 
    \n";
    }
//test area can be deleted
foreach ($ParentComments as $key => $toplevelcomment)
{
 	echo $toplevelcomment  ;
 	

 	//Ouput next level of comments  
 	foreach ($replies as $childKey => $childReply)
 	{
 	    if ($key == $childKey)
 	    {
 	        echo $childReply ; 
 	    } 
 	}
 	//It is this point that I would now like to check the $replies array
 	//Then if the $key matches the array key of $replies array
 	//echo that comment
 	
 	
 	} 
 	

//end test area 

    
/* proper output 


foreach ($ParentComments as $key => $reply)
{
 	echo $reply  ;

	foreach ($replies as $childKey => $childReply)
	{ 
	if ($key == $childKey)
	{ 
	echo $childReply ; 
		foreach ($Level2 as $Key2 => $Level2Reply)
		{ 
		if ($Key2 == $childKey) 
			{
				echo $Level2Reply ; 
 			} 
		
		}
	}
	}
}//foreach loop 
  */
		}
	}// End Functio

}
//********************************************************************// 
//*******************End CLASS****************************************// 
//********************************************************************// 
