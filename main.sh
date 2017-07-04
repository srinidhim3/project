sql () {
table=$1
price=$2
usd=$3
high=$4
low=$5
error=$(mysql --user="commuser" --password="ka#4@16#" --database="commoditydb" <<<"insert into $table(DT,last_price,last_price_inr,high_price,low_price) values (current_timestamp(),$price,$price * $usd,$high,$low)" 2>&1 1>/dev/null)

if [ "$?" != "0" ]; then
    dt=$(date)
    echo "$table insert failed at $dt Error: $error" 1>&2 >> error_comm.log
else
	dt=$(date)
	echo "$table details inserted at $dt"
fi
}

download () {
	wget "http://comrates.in.forexprostools.com/index.php?force_lang=56&pairs_ids=49768;8831;8849;8830;8862;959208;8836;956470;&header-text-color=%23FFFFFF&curr-name-color=%230059b0&inner-text-color=%23000000&green-text-color=%232A8215&green-background=%23B7F4C2&red-text-color=%23DC0001&red-background=%23FFE2E2&inner-border-color=%23CBCBCB&border-color=%23cbcbcb&bg1=%23F6F6F6&bg2=%23ffffff&open=hide&month=hide&change=hide&change_in_percents=hide&last_update=hide" -O output -q
	
	wget "http://fxrates.in.forexprostools.com/index.php?force_lang=56&pairs_ids=160;&header-text-color=%23FFFFFF&curr-name-color=%230059b0&inner-text-color=%23000000&green-text-color=%232A8215&green-background=%23B7F4C2&red-text-color=%23DC0001&red-background=%23FFE2E2&inner-border-color=%23CBCBCB&border-color=%23cbcbcb&bg1=%23F6F6F6&bg2=%23ffffff&bid=show&ask=hide&last=hide&open=hide&high=hide&low=hide&change=hide&change_in_percents=hide&last_update=hide" -O usd -q
}

comm[0]='aluminium'
comm[1]='copper'
comm[2]='crude'
comm[3]='gold'
comm[4]='natural'
comm[5]='nickel'
comm[6]='silver'
comm[7]='zinc'

alu=0
cop=0
cru=0
gol=0
nat=0
nic=0
sil=0
zin=0

while :
do

download

STR=$(grep USD "usd")
usd=${STR:711:6}
usd=$(echo $usd | grep -Eo '[0-9]+([.][0-9]+)?')

for i in "${comm[@]}"
do
	
	if [ $i == 'aluminium' ] 
	then
		STR=$(grep Aluminium "output")
		price=${STR:262:8}
		high=${STR:338:8}
		low=${STR:413:8}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}
		
		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price != $alu ]
		then
			sql $i $price $usd $high $low
		fi
		
		alu=$price
	fi
	
	if [ $i == 'copper' ] 
	then
		STR=$(grep Copper "output")
		price=${STR:259:5}
		high=${STR:331:5}
		low=${STR:402:5}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}

		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $cop ]
		then
			sql $i $price $usd $high $low
		fi
		
		cop=$price
	fi

	if [ $i == "crude" ]
	then
		STR=$(grep Crude "output")
		price=${STR:267:5}
		high=${STR:339:5}
		low=${STR:410:5}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}

		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $cru ]
		then
			sql $i $price $usd $high $low
		fi
		
		cru=$price
	fi

	if [ $i == "gold" ]
	then
		STR=$(grep Gold "output")
		price=${STR:256:8}
		high=${STR:331:8}
		low=${STR:405:8}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}

		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $gol ]
		then
			sql $i $price $usd $high $low
		fi
		
		gol=$price	
	fi

	if [ $i == "natural" ]
	then
		STR=$(grep Natural "output")
		price=${STR:269:5}
		high=${STR:341:5}
		low=${STR:412:5}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}
		
		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $nat ]
		then
			sql ngas $price $usd $high $low
		fi
		
		nat=$price	
	fi
	
	if [ $i == "nickel" ]
	then
		STR=$(grep Nickel "output")
		price=${STR:260:9}
		high=${STR:338:9}
		low=${STR:415:9}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}
		
		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $nic ]
		then
			sql $i $price $usd $high $low
		fi
		
		nic=$price	
	fi
	
	if [ $i == "silver" ]
	then
		STR=$(grep Silver "output")
		price=${STR:259:6}
		high=${STR:332:6}
		low=${STR:404:6}

		price=${price//,}
		high=${high//,}
		low=${low//,}
		
		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $sil ]
		then
			sql $i $price $usd $high $low
		fi
		
		sil=$price	
	fi
	
	if [ $i == "zinc" ]
	then
		STR=$(grep Zinc "output")
		price=${STR:258:8}
		high=${STR:335:8}
		low=${STR:411:8}
		
		price=${price//,}
		high=${high//,}
		low=${low//,}
		
		price=$(echo $price | grep -Eo '[0-9]+([.][0-9]+)?')
		high=$(echo $high | grep -Eo '[0-9]+([.][0-9]+)?')
		low=$(echo $low | grep -Eo '[0-9]+([.][0-9]+)?')
		
		if [ $price !=  $zin ]
		then
			sql $i $price $usd $high $low
		fi
		
		zin=$price	
	fi
done

rm -f output
rm -f usd

done
