import sys, os, string

# 중간의 '보조 기억 장치'를 처리하는 편의 함수
def touchopen(filename, *args, **kwargs):
    try:
        os.remove(filename)
    except OSError:
        pass
    open(filename, "a").close() # 파일을 '수정'한다
    return open(filename, *args, **kwargs)

# 메모리 제약은 크기가 1024 이하다
data = []
# 우리는 행운아다:
# 의미 없는 단어는 556자에 불과하고 모든 줄은
# 80자 미만이므로 문제를 단순화할 수 있다.
# 즉, 한 번에 한 줄씩 입력받아 처리하는 동안
# 의미 없는 단어를 메모리에 둘 수 있다.
# 이 두 가정이 유효하지 않으면 알고리즘을
# 상당히 변경해야 한다.

# 전체 전략: (부분 1) 입력 파일을 읽어 단어를 세고
# 보조 기억 장치(파일)에 횟수를 증가/저장한다
# (부분2) 보조 기억 장치에서 가장 빈도가 높은 단어 25개를 찾는다

# 부분 1:
# - 입력 파일에서 한 번에 한 줄씩 읽는다
# - 문자를 걸러낸 후 소문자로 정규화한다
# - 단어를 식별하고 파일에서 해당하는 횟수를 증가시킨다

# 의미 없는 단어 목록을 읽어 들인다
f = open('stop_words.txt')
data = [f.read(1024).split(',')] # data[0]에는 의미 없는 단어가 있다
f.close()

data.append([])     # data[1]은 (최대 80자인) 줄
data.append(None)   # data[2]는 단어의 시작 문자 색인
data.append(0)      # data[3]은 문자에 대한 색인이며 i = 0
data.append(False)  # data[4]는 단어를 찾았는지 여부를 나타내는 플래그
data.append('')     # data[5]는 해당 단어
data.append('')     # data[6]은 단어, NNNN
data.append(0)      # data[7]은 빈도

# 보조 기억 장치를 연다
word_freqs = touchopen('word_freqs', 'rb+')
# 입력 파일을 연다
f = open(sys.argv[1])
# 입력 파일 내의 각 줄을 순회
while True:
    data[1] = [f.readline()]
    if data[1] == ['']: # 입력 파일 끝
        break
    if data[1][0][len(data[1][0])-1] != '\n': # \n으로 끝나지 않으면
        data[1][0] = data[1][0] + '\n'          # \n을 추가한다.
    data[2] = None
    data[3] = 0
    # 해당 줄 내 문자를 순회
    for c in data[1][0]: # 기호 c를 제거하는 것은 연습 문제로 남긴다
        if data[2] == None:
            if c.isalnum():
                # 단어의 시작을 찾았다
                data[2] = data[3]
        else:
            if not c.isalnum():
                # 단어 끝을 찾았으므로 처리한다
                data[4] = False
                data[5] = data[1][0][data[2]:data[3]].lower()
                # len < 2인 단어와 의미 없는 단어를 무시한다
                if len(data[5]) >= 2 and data[5] not in data[0]:
                    # 이미 존재하는지 확인한다
                    while True:
                        data[6] = str(word_freqs.readline().strip(), 'utf-8')
                        if data[6] == '':
                            break
                        data[7] = int(data[6].split(',')[1])
                        # 공백 문자가 없는 단어
                        data[6] = data[6].split(',')[0].strip()
                        if data[5] == data[6]:
                            data[7] += 1
                            data[4] = True
                            break
                    if not data[4]:
                        word_freqs.seek(0, 1) # 윈도우에서는 필요하다
                        word_freqs.write(bytes("%20s,%04d\n" % (data[5], 1), 'utf-8'))
                    else:
                        word_freqs.seek(-26, 1)
                        word_freqs.write(bytes("%20s,%04d\n" % (data[5], data[7]), 'utf-8'))
                    word_freqs.seek(0,0)
                # 초기화하자
                data[2] = None
        data[3] += 1
# 입력 파일 처리를 마쳤다
f.close()
word_freqs.flush()

# 부분 2
# 이제 가장 자주 나온 단어 25개를 찾아야 한다.
# 메모리에 있는 이전 값은 전혀 필요 없다.
del data[:]

# 상위 25개 단어에 대해 처음 25개 항목만 사용하자
data = data + [[]]*(25 - len(data))
data.append('')     # data[25]는 파일에서 읽은 단어, 빈도
data.append(0)      # data[26]은 빈도

# 보조 기억 장치(파일)을 순회한다.
while True:
    data[25] = str(word_freqs.readline().strip(), 'utf-8')
    if data[25] == '': # EOF
        break
    data[26] = int(data[25].split(',')[1])      # 정수로 읽는다
    data[25] = data[25].split(',')[0].strip()   # 단어
    # 이 단어가 메모리에 있는 단어들보다 횟수가 더 많은지 확인한다
    for i in range(25): # 기호 i를 제거하는 것은 연습 문제로 남긴다
        if data[i] == [] or data[i][1] < data[26]:
            data.insert(i, [data[25], data[26]])
            del data[26] # 마지막 요소를 삭제한다
            break

for tf in data[0:25]: # 기호 tf를 제거하는 것은 연습 문제로 남긴다
    if len(tf) == 2:
        print(tf[0], ' - ', tf[1])
# 모두 마쳤다
word_freqs.close()