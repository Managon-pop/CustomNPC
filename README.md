# CustomNPC
##Custom NPC in your Server!!
NPCはあなたにアイテムを授け、コマンドを実行します。<br />
そのコマンドは設定で変えることができます.
###使い方
###設定の変更
###コマンド
###Todo
###Entity ID List

##使い方
プレイヤー型のNPCを設置したい場合は次のようにコマンドをうちます。<br />
<strong>/cn add player &lt;ItemId&gt; &lt;command&gt; &lt;Name&gt;</strong>
そして地面をタップしてください。<br /><br />
また、エンティティのNPCを設置する場合は次のようにしてください。<br />
<strong>/cc add &lt;ENTITY LIST&gt; &lt;ItemID&gt;</strong>
<br />もし消す場合は
<br /><strong>/cn del</strong>そして対象のNPCをタップします.
##設定の変更のやり方
NPC.jsonを開きます。<br />
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/picc.png"></img>
<br />コマンドを変更する場合、"command"をこのようにします。
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/co.png"></img>
<br />そしてタップすると...<br />
<img src="https://github.com/Managon-pop/CustomNPC/blob/master/img/nana.jpg"></img><br />
反映されています。<br />
同じように名前やアイテムを変えられます。.
##コマンド
/cn add &lt;player | entityId&gt; &lt;ItemID&gt; &lt;Name&gt;<br />
/cn del
/cn opt move &lt;x&gt; &lt;y&gt; &lt;z&gt;
        item &lt;ID&gt; &lt;Amount&gt;
        name &lt;Name&gt;
        msg &lt;Message&gt;
        command &lt;add | del&gt; &lt;Command&gt;
##Todo
<ul>
<li>オプションをゲーム内で変えられるように。</li>
</ul>
##Entity ID LIST
Arrow : 80<br />Bat : 19<br />Blaze : 43<br />Boat : 90<br />CaveSpider : 40<br />Chicken : 10<br />Cow : 11<br />Creeper : 33<br />Enderman : 38<br />Ghast : 41<br />IronGolem : 20<br />Mooshroom : 16<br />Ocelot : 22<br />Pig : 12<br />PigZombie : 36<br />Rabbit : 18<br />Sheep : 13<br />SilverFish : 39<br />Skelton : 34<br />SnowBall : 81<br />SnowGolem : 21<br />Spider : 35<br />Squid : 17<br />Villager : 16<br />Wolf : 14<br />Zombie : 32<br />ZombieVillager : 44
##アップデート情報
ß1.0.0...公開
ß1.1.0...コマンドの追加<br />
バグ修正<br />
ゲーム内で設定の変更を可能に<br />
二つの角度を保存
