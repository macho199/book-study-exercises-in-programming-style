# 프로그래밍 패턴(exercises in programming style)

## 단어 빈도
> 텍스트 파일이 제공되면 가장 빈도가 높은 단어 N(예를 들면 25)개와 그에 해당하는 빈도를 내림차순으로 출력한다. 대문자는 모두 소문자로 정규화하고 'the,' 'for' 등과 같은 의미 없는 단어는 무시해야 한다. 문제를 단순하게 만들기 위해 빈도가 같은 단어의 순서는 신경 쓰지 않는다. 이 계산 작업을 단어 빈도라 한다. 다음은 입력 파일과 이 입력에 대해 단어 빈도를 계산한 후 출력한 예다.

```bash
Input:
  Write tigers live mostly in India
  Wild lions live mostly in africa

Output:
  live  -  2
  mostly  -  2
  africa  -  1
  india  -  1
  lions  -  1
  tigers  -  1
  white  -  1
  wild  - 1
```
> 구텐베르크(Gutenberg) 프로젝트에 있는 제인 오스틴(Jane Austen)의 오만과 편견(Pride and Prejudice)을 대상으로 이 단어 빈도를 실행하면 다음과 같은 출력결과를 얻을 수 있다.

```bash
mr  -  786
elizabeth  -  635
very  -  488
darcy  -  418
such  -  395
mrs  -  343
much  -  329
more  -  327
bennet  -  323
bingley  -  306
jane  -  295
miss  -  283
one  -  275
know  -  239
before  -  229
herself  -  227
though  -  226
well  -  224
never  -  220
sister  -  218
soon  -  216
think  -  211
now  -  209
time -  203
good  -  201
```

## 단어 색인
> 텍스트 파일이 제공되면 모든 단어를, 해당 단어가 나온 쪽 번호와 함께 알파벳 순으로 출력한다. 쪽 번호는 100번째까지만 표시하고 한 쪽은 45줄로 가정한다. 예를 들면, 오만과 편견에 대한 색인 중 처음 몇 가지는 다음과 같을 것이다.
```bash
abatement  -  89
abhorrence  -  101, 145, 152, 241, 274, 281
abhorrent  -  253
abide  -  158, 292
...
```
## 문맥상 단어
> 텍스트 파일이 제공되면 어떤 단어를, 해당 단어가 나온 쪽 번호와 함께 그 단어 앞뒤 내용을 포함해 알파벳순으로 표시한다. 이때 한 쪽은 45줄로 가정하고, 해당 단어 전후로 각 두 단어씩 포함하며 구두점은 무시한다. 오만과 편견을 예로 들면, 'concealment'와 'hurt' 두 단어에 대한 결과는 다음과 같다.
```bash
output:
  perhaps this concealment this disguise  -  150
  purpose of concealment for no  -  207
  pride was hurt he suffered  -  87
  must be hurt by such  -  95
  and are hurt if i  -  103
  pride been hurt by my  -  145
  must be hurt by such  -  157
  infamy was hurt and distressed  -  248
```
> 문맥상 단어 작업용으로 제안하는 단어는 concealment, discontented, hurt, agitation, mortifying, reproach, unexpected, indignation, mistake, confusion이다.