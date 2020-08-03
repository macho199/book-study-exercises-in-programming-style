
const fs = require('fs');
const readline = require('readline');

function touchopen(filename, ...args) {
	if (fs.existsSync(filename)) {
		fs.unlinkSync(filename);
	}

	fs.closeSync(fs.openSync(filename, 'a'));
	return fs.openSync(filename, ...args);
}

let data = [];
data = [fs.readFileSync('stop_words.txt', {encoding: 'utf-8'})];

data.push([]);			// data[1]은 (최대 80자인) 줄
data.push(null);		// data[2]는 단어의 시작 문자 색인
data.push(0);				// data[3]은 문자에 대한 색인이며 i = 0
data.push(false);		// data[4]는 단어를 찾았는지 여부를 나타내는 플래그
data.push('');			// data[5]는 해당 단어
data.push('');			// data[6]은 단어, NNNN
data.push(0);				// data[7]은 빈도
data.push(0);				// data[8]은 보조기억 장치의 현재까지 읽은 줄 색인

touchopen('word_freqs');

const rl = readline.createInterface(
	fs.createReadStream(process.argv[2]), 
	process.stdout
);

rl.on('line', line => {
	data[1] = [line];
	data[2] = null;
	data[3] = 0;

	data[1][0].split('').forEach(c => {
		if (data[2] == null) {
			if (c.match(/[\dA-Za-z]/)) {
				data[2] = data[3];
			}
		} else {
			if (!c.match(/[\dA-Za-z]/)) {
				data[4] = false;
				data[5] = data[1][0].substring(data[2], data[3]).toLowerCase();
				
				if (data[5].length >= 2 && data[0].indexOf(data[5]) < 0) {
					console.log(data[5]);
					let aaa = (fd, data) => {
						return new Promise((resolve, reject) => {
							fs.read(fd, {length: 25}, (err, bytesRead, buffer) => {
								// console.log(data[5]);
								// aaa(data);
								if (err) {
									resolve();
								}

								data[6] = buffer.toString('utf-8').trim();

								if (data[6] == '') {
									resolve();
								}

								data[7] = Number(data[6].split(',')[1]);
								data[6] = data[6].split(',')[0].trim();

								if (data[5] == data[6]) {
									data[7]++;
									data[4] = true;
								}

								resolve(aaa());
							});
						});
					};

					const fd = fs.openSync('word_freqs');
					//let bb = await aaa(fd, data);
					aaa(fd, data).then(() => {

					});

					// let rlsub = readline.createInterface(
					// 	fs.createReadStream('word_freqs'),
					// 	process.stdout
					// );
					// data[8] = 0;

					// rlsub.on('line', line => {
					// 	data[8]++;
					// 	data[6] = line.trim();
					// 	if (data[6] == '') {
					// 		rlsub.close();
					// 		return;
					// 	}
						
					// 	data[7] = Number(data[6].split(',')[1]);
					// 	data[6] = data[6].split(',')[0].trim();

					// 	if (data[5] == data[6]) {
					// 		data[7]++;
					// 		data[4] = true;
					// 		rlsub.close();
					// 		return;
					// 	}
					// });

					// rlsub.on('close', () => {
					// 	// (data[8] - 1) * 25
					// 	let fws = fs.createWriteStream('word_freqs', {
					// 		flags: 'r+',
					// 		encoding: 'utf-8',
					// 		start: data[4] ? (data[8] - 1) * 25 : data[8] * 25
					// 	});

					// 	if (!data[4]) {
					// 		fws.write(data[5].padStart(20) + ',' + '1'.padStart(4, '0'));
					// 	} else {
					// 		fws.write(data[5].padStart(20) + ',' + String(data[7]).padStart(4, '0'));
					// 	}
						
					// 	fws.close();

					// 	// if (!data[4]) {
					// 	// 	substream.write(data[6].padStart(20) + ',' + String(data[7]).padStart(4, '0'));
					// 	// }
					// });
				}

				data[2] = null;
			}
		}
		data[3]++;
	});
});

rl.on('close', () => {
	console.log('end!');
});
