# CustomNPC
##Custom NPC in your Server!!
NPC sends you Item and does command.<br />
You can set Item and Command.
###Usage
###How to set up
###Commands
###Todo
###Entity ID List
###Updates

##Usage
If you want to add Player's NPC, enter command in game <br />
<strong>/cn add player &lt;ItemId&gt; &lt;command(If you don't want, enter cn)&gt; &lt;Name&gt;</strong>
then tap the ground.<br /><br />
Or If you want to add Entity NPC, enter command<br />
<strong>/cn add &lt;player | entityId&gt; &lt;ItemID&gt; &lt;Name&gt;</strong>
<br />If you delete NPC, enter
<br /><strong>/cn del</strong>&nbsp;and Tap.
##How to set up Item and command?
Open NPC.json and follow.<br />
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/picc.png"></img>
<br />If you want to change command, change at "command". Like this,
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/co.png"></img>
<br />And then go to your server and tap NPC.<br />
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/nana.jpg"></img><br />
And If you want to change its name, change at "name".
##Commands
/cn add &lt;player | entityId&gt; &lt;ItemID&gt; &lt;Name&gt;<br />
/cn del
/cn opt move &lt;x&gt; &lt;y&gt; &lt;z&gt;
        item &lt;ID&gt; &lt;Amount&gt;
        name &lt;Name&gt;
        msg &lt;Message&gt;
        command &lt;add | del&gt; &lt;Command&gt;
##Todo
<ul>
<li>Change options of npc in game.</li>
<li>Does some commands(now only 1)</li>
<li>NPC always look player</li>
<li>Message</li>
<li>If the player is near the NPC, send Message on its name . </li>
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
v1.1.3...Fixed bug(When you tap NPC, it gets your skin)
