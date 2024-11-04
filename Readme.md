# Cistercian manipulator

## Description

This program was created to help solve the second enigma of "Le Tresor de Gisor" treasure hunt.

Cistercian numbers are a numeral system that was used by the Cistercian monks in the Middle Ages. It use a set of lines to represent numbers from 1 to 9999.

![Cistercian numbers](Cistercian%20numbers.png "Cistercian numbers")

The enigma has Roman numerals and a Cistercian number. We assumed that we needed to convert the Romans numerals to Arabic numerals and then to Cistercian numbers and assemble the glyphs to form some word or number.

It turned out to be a very wrong assumption which was invalidated by the author of the treasure hunt.

You can use it for other enigma that may use this mechanism.

## Requirements

[Docker](https://www.docker.com/)

## Installation

```bash
make install
```

## Usage

You can customize the .env.local file to alter the design of the glyphs.

### Generate all Cistercian numbers from 1 to 9999

```bash
make generate
```

### Playground

```bash
make merge
```

Alternatively if you have [imv](https://github.com/eXeC64/imv) installed you can run the following command to generate the numbers then view them in a gallery;

```bash
make merge-view
```

### Local CI

```bash
make cc
```

## Example output

Difference

<img src="examples/0-output-difference.png" width='500' />

Difference truncated

<img src="examples/0-output-difference-truncated.png" width='500' />

Side by Side unmerged

<img src="examples/1-output-side-to-side-unmerged.png" width='500' />

Side by Side unmerged truncated

<img src="examples/1-output-side-to-side-unmerged-truncated.png" width='500' />

Side by Side merged

<img src="examples/2-output-side-to-side-merged.png" width='500' />

Side by Side merged truncated

<img src="examples/2-output-side-to-side-merged-truncated.png" width='500' />

Multi line merged

<img src="examples/3-output-multiple-lines-merged.png" width='100' />

Multi line unmerged

<img src="examples/4-output-multiple-lines-unmerged.png" width='100' />

Multi line shift unmerged full space

<img src="examples/5-output-multiple-lines-shifted-unmerged-full-space.png" width='100' />

Multi line shift unmerged half space

<img src="examples/6-output-multiple-lines-shifted-unmerged-half-space.png" width='100' />

Multi line shift merged full space

<img src="examples/7-output-multiple-lines-shifted-merged-full-space.png" width='100' />

Multi line shift merged half space

<img src="examples/8-output-multiple-lines-shifted-merged-half-space.png" width='100' />