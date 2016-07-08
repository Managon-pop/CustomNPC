# CustomNPC
##Custom NPC in your Server!!

CustomNPC can add NPCs.<br />
You can tap the NPCs to use commands and get informations.

##Usage

<h3>To add Player NPC with your skin,</h3> 
<strong>/cn add &lt;player&gt; &lt;ItemId(add to tapper)&gt; &lt;command(If you want none, enter cn)&gt; &lt;NPC's Name&gt;</strong><br />
<h3>To add Entities NPC,</h3>
<strong>/cn add &lt;entityId&gt; &lt;ItemId(add to tapper)&gt; &lt;command(If you want none, enter cn)&gt; &lt;NPC's Name&gt;</strong><br />
To delete NPC, enter<br />
<strong>/cn del</strong>&nbsp;and Tap.<br />

<h3>To set Options</h3>
Enter commands for you want, and Tap NPC.<br />
<h4>&nbsp;&nbsp;&nbsp;Set Name</h4>
<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/cn opt name &lt;Name&gt;</strong>
<h4>&nbsp;&nbsp;&nbsp;Set Message</h4>
<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/cn opt msg &lt;Message&gt;</strong>
<h4>&nbsp;&nbsp;&nbsp;Set Position</h4>
<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/cn opt move &lt;X&gt; &lt;Y&gt; &lt;Z&gt;</strong>
<h4>&nbsp;&nbsp;&nbsp;Set Item</h4>
<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/cn opt item &lt;id&gt; &lt;amount&gt;</strong>
<h4>&nbsp;&nbsp;&nbsp;Add command</h4>
<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/cn opt command add &lt;command&gt;</strong>
<h4>&nbsp;&nbsp;&nbsp;Delete command</h4>
<strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/cn opt command del &lt;command&gt;</strong>

##How to set up options at NPC.json?
Open NPC.json and follow.<br />
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/picc.png"></img>
<br />If you want to change command, change at "command". Like this,
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/co.png"></img>
<br />And then go to your server and tap NPC.<br />
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/nana.jpg"></img><br />
And If you want to change its name, change at "name".
##Todo
<ul>
<li>If the player is near the NPC, send Message on its name . </li>//top
</ul>
##Entity ID LIST
Arrow : 80<br />Bat : 19<br />Blaze : 43<br />Boat : 90<br />CaveSpider : 40<br />Chicken : 10<br />Cow : 11<br />Creeper : 33<br />Enderman : 38<br />Ghast : 41<br />IronGolem : 20<br />Mooshroom : 16<br />Ocelot : 22<br />Pig : 12<br />PigZombie : 36<br />Rabbit : 18<br />Sheep : 13<br />SilverFish : 39<br />Skelton : 34<br />SnowBall : 81<br />SnowGolem : 21<br />Spider : 35<br />Squid : 17<br />Villager : 16<br />Wolf : 14<br />Zombie : 32<br />ZombieVillager : 44
##updates
ß1.0.0... First.<br />
ß1.1.0...・When the player tap NPC, It sends Message.<br />
         ・Change option in a game with /cn opt.<br />
         ・Save yaw and pitch.<br />
         ・fix when you delete the player NPC, It clashes.<br />
v1.1.1...Add #1(Dont use!)<br />
v1.1.2...Fixed bug and add #1<br />
v1.1.3...Fixed bug(When you tap NPC, it gets your skin)<br />
v1.2.0...Updated for 1.15.0 and PocketMine ver 2.0.0
