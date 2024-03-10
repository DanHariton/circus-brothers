<?php

namespace App\Service\Image;

use Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class ImageUploader
{
    const TYPE_1050x770 = 1;

    const TYPE_1280x800 = 2;

    const TYPE_1920x890 = 3;

    /** @var array|int[] $size_1050x770 */
    private static array $size_1050x770 = [1050, 770];

    /** @var array|int[] $size_1280x800 */
    private static array $size_1280x800 = [1280, 800];

    /** @var array|int[] $size_1920x890 */
    private static array $size_1920x890 = [1920, 890];

    private string $targetDirectoryImg;
    private Filesystem $filesystem;
    private ImageResizer $resizer;

    public function __construct(string $targetDirectoryImg, Filesystem $filesystem, ImageResizer $resizer)
    {
        $this->targetDirectoryImg = $targetDirectoryImg;
        $this->resizer = $resizer;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $fileName
     */
    public function remove(string $fileName): void
    {
        $this->filesystem->remove($this->getTargetDirectory() . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @throws Exception
     */
    public function upload(UploadedFile $file, int $imageType = 0): string
    {
        $fileName = Uuid::v4() . 'Service' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);

            $newPath = $this->getTargetDirectory() . DIRECTORY_SEPARATOR . $fileName;
            $this->resizer->setImage($newPath);
            $size = match ($imageType) {
                3 => self::$size_1920x890,
                2 => self::$size_1280x800,
                default => self::$size_1050x770,
            };
            $this->resizer->resizeTo(...$size);
            $this->resizer->save($newPath);

        } catch (FileException $e) {
            throw new Exception('Soubor se nepodařilo uložit, ' . $e->getMessage());
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectoryImg;
    }
}