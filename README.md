# psh-packager
Package composer cli applications into a PSH

## Currently working
- Symfony

## How to use
```bash
git clone <project> project
./build <stub type> <project name>
```

For example:
```bash
git clone git://link.to/repo.git project
./build symfony toolchain
```

### Symfony requirements
- symfony/filesystem
- symfony/finder