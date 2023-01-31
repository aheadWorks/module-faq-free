<?php

namespace Aheadworks\FaqFree\Test\Unit\Model;

use Aheadworks\FaqFree\Model\ImageUploader;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Write as DirectoryWrite;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for ImageUploader
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImageUploaderTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Core file storage database
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|Database
     */
    private $coreFileStorageDatabaseMock;

    /**
     * Uploader factory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|UploaderFactory
     */
    private $uploaderFactoryMock;

    /**
     * Store manager
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|LoggerInterface
     */
    private $loggerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    private $filesystemMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DirectoryWrite
     */
    private $directoryWriteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Uploader
     */
    private $uploaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Store
     */
    private $storeMock;

    /**
     * @var string
     */
    private $baseTmpPath;

    /**
     * @var ImageUploader
     */
    private $imageUploaderObject;

    /**
     * Initialize ImageUploader model
     */
    protected function setUp(): void
    {
        $this->baseTmpPath = '/base/tmp/path';

        $this->objectManager = new ObjectManager($this);

        $this->storeMock = $this->createMock(Store::class);
        $this->uploaderFactoryMock = $this->createMock(UploaderFactory::class);
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->uploaderMock = $this->createMock(Uploader::class);
        $this->directoryWriteMock = $this->createMock(DirectoryWrite::class);
        $this->coreFileStorageDatabaseMock = $this->createMock(Database::class);

        $this->filesystemMock
            ->expects($this->once())
            ->method('getDirectoryWrite')
            ->with('media')
            ->willReturn($this->directoryWriteMock);

        $this->imageUploaderObject = $this->objectManager->getObject(
            ImageUploader::class,
            [
                'coreFileStorageDatabase' => $this->coreFileStorageDatabaseMock,
                'uploaderFactory' => $this->uploaderFactoryMock,
                'filesystem' => $this->filesystemMock,
                'storeManager' => $this->storeManagerMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Move file from temp directory
     *
     * @covers ImageUploader::moveFileFromTmp
     */
    public function testMoveFrom()
    {
        $imageName = 'imageName';
        $baseImagePath = 'faq/imageName';
        $baseTmpImagePath = 'tmp/faq/imageName';

        $this->directoryWriteMock
            ->expects($this->at(0))
            ->method('isExist')
            ->with($baseImagePath)
            ->willReturn(false);

        $this->directoryWriteMock
            ->expects($this->at(1))
            ->method('isExist')
            ->with($baseTmpImagePath)
            ->willReturn(true);

        $this->coreFileStorageDatabaseMock
            ->expects($this->once())
            ->method('copyFile')
            ->with($baseTmpImagePath, $baseImagePath);

        $this->directoryWriteMock
            ->expects($this->once())
            ->method('renameFile')
            ->with($baseTmpImagePath, $baseImagePath);

        $this->assertEquals($imageName, $this->imageUploaderObject->moveFileFromTmp($imageName));
    }

    /**
     * Image already has been saved
     *
     * @covers ImageUploader::moveFileFromTmp
     */
    public function testImageExistInMediaDirectory()
    {
        $imageName = 'imageName';
        $baseTmpImagePath = 'tmp/faq/imageName';
        $baseImagePath = 'faq/imageName';

        $this->directoryWriteMock
            ->expects($this->at(0))
            ->method('isExist')
            ->with($baseImagePath)
            ->willReturn(false);

        $this->directoryWriteMock
            ->expects($this->at(1))
            ->method('isExist')
            ->with($baseTmpImagePath)
            ->willReturn(true);

        $this->assertEquals($imageName, $this->imageUploaderObject->moveFileFromTmp($imageName));
    }

    /**
     * Move file from temp directory
     * Catch exception when temp image path is not exist
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Image do not exist
     * @covers ImageUploader::moveFileFromTmp
     */
    public function testMoveFromNotExistPatch()
    {
        $imageName = 'imageName';
        $baseTmpImagePath = 'tmp/faq/imageName';

        $this->directoryWriteMock
            ->expects($this->at(1))
            ->method('isExist')
            ->with($baseTmpImagePath)
            ->willReturn(false);

        $this->coreFileStorageDatabaseMock
            ->expects($this->never())
            ->method('copyFile')
            ->withAnyParameters();

        $this->directoryWriteMock
            ->expects($this->never())
            ->method('renameFile')
            ->withAnyParameters();

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->assertEquals($imageName, $this->imageUploaderObject->moveFileFromTmp($imageName));
    }

    /**
     * Move file from temp directory
     * Internal method renameFile() can throw FileSystemException
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @covers ImageUploader::moveFileFromTmp
     */
    public function testMoveFromExceptionOnRenameFile()
    {
        $imageName = 'imageName';
        $baseImagePath = 'faq/imageName';
        $baseTmpImagePath = 'tmp/faq/imageName';

        $this->directoryWriteMock
            ->expects($this->at(0))
            ->method('isExist')
            ->with($baseImagePath)
            ->willReturn(false);

        $this->directoryWriteMock
            ->expects($this->at(1))
            ->method('isExist')
            ->with($baseTmpImagePath)
            ->willReturn(true);

        $this->coreFileStorageDatabaseMock
            ->expects($this->once())
            ->method('copyFile')
            ->with($baseTmpImagePath, $baseImagePath);

        $this->directoryWriteMock
            ->expects($this->once())
            ->method('renameFile')
            ->willThrowException(new FileSystemException(__('Test Phrase')));

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->imageUploaderObject->moveFileFromTmp($imageName);
    }

    /**
     * Move file from temp directory
     * Internal method copyFile() may throw exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @covers ImageUploader::moveFileFromTmp
     */
    public function testMoveFromExceptionOnCopyFile()
    {
        $imageName = 'imageName';
        $baseImagePath = 'faq/imageName';
        $baseTmpImagePath = 'tmp/faq/imageName';

        $this->directoryWriteMock
            ->expects($this->at(0))
            ->method('isExist')
            ->with($baseImagePath)
            ->willReturn(false);

        $this->directoryWriteMock
            ->expects($this->at(1))
            ->method('isExist')
            ->with($baseTmpImagePath)
            ->willReturn(true);

        $this->coreFileStorageDatabaseMock
            ->expects($this->once())
            ->method('copyFile')
            ->willThrowException(new \Exception());

        $this->directoryWriteMock
            ->expects($this->never())
            ->method('renameFile');

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->imageUploaderObject->moveFileFromTmp($imageName);
    }

    /**
     * Save file to temp directory
     *
     * @covers ImageUploader::saveFileToTmpDir
     */
    public function testSaveFileToTmpDir()
    {
        $fileId = 3;
        $result = ['tmp_name' => '/tmp/name', 'path' => '/path/to/file', 'file' => 'filename'];

        $this->uploaderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileId])
            ->willReturn($this->uploaderMock);

        $this->uploaderMock
            ->expects($this->once())
            ->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png']);

        $this->uploaderMock
            ->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true);

        $this->directoryWriteMock
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with('tmp/faq')
            ->willReturn($this->baseTmpPath);

        $this->uploaderMock
            ->expects($this->once())
            ->method('save')
            ->with($this->baseTmpPath)
            ->willReturn($result);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with('media')
            ->willReturn('http://example.com/path/to/faq/');

        $relativePath = 'tmp/faq/filename';

        $this->coreFileStorageDatabaseMock
            ->expects($this->once())
            ->method('saveFile')
            ->with($relativePath);

        $result = array_merge(
            ['name' => $result['file'], 'url' => 'http://example.com/path/to/faq/tmp/faq/filename'],
            $result
        );

        $this->assertEquals($result, $this->imageUploaderObject->saveFileToTmpDir($fileId));
    }

    /**
     * Save file to temp directory
     * If file can't be saved into destination directory throws Exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @covers ImageUploader::saveFileToTmpDir
     */
    public function testSaveFileToTmpDirWithExceptionWhileFileSaving()
    {
        $fileId = 3;
        $result = ['tmp_name' => '/tmp/name', 'path' => '/path/to/file', 'file' => 'filename'];

        $this->uploaderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileId])
            ->willReturn($this->uploaderMock);

        $this->uploaderMock
            ->expects($this->once())
            ->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png']);

        $this->uploaderMock
            ->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true);

        $this->directoryWriteMock
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with('tmp/faq')
            ->willReturn($this->baseTmpPath);

        $this->uploaderMock
            ->expects($this->once())
            ->method('save')
            ->with($this->baseTmpPath)
            ->willReturn($result);

        $this->storeManagerMock
            ->expects($this->once())
            ->method('getStore')
            ->willReturn($this->storeMock);

        $this->storeMock
            ->expects($this->once())
            ->method('getBaseUrl')
            ->with('media')
            ->willReturn('http://example.com/path/to/faq/');

        $this->coreFileStorageDatabaseMock
            ->expects($this->once())
            ->method('saveFile')
            ->willThrowException(new \Exception());

        $this->loggerMock
            ->expects($this->once())
            ->method('critical');

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->imageUploaderObject->saveFileToTmpDir($fileId);
    }

    /**
     * Save file to temp directory
     * If file can't be saved into database throws Exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @covers ImageUploader::saveFileToTmpDir
     */
    public function testSaveFileToTmpDirWithNoResult()
    {
        $fileId = 3;
        $result = false;

        $this->uploaderFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileId])
            ->willReturn($this->uploaderMock);

        $this->uploaderMock
            ->expects($this->once())
            ->method('setAllowedExtensions')
            ->with(['jpg', 'jpeg', 'gif', 'png']);

        $this->uploaderMock
            ->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true);

        $this->directoryWriteMock
            ->expects($this->once())
            ->method('getAbsolutePath')
            ->with('tmp/faq')
            ->willReturn($this->baseTmpPath);

        $this->uploaderMock
            ->expects($this->once())
            ->method('save')
            ->with($this->baseTmpPath)
            ->willReturn($result);

        $this->storeManagerMock
            ->expects($this->never())
            ->method('getStore');

        $this->storeMock
            ->expects($this->never())
            ->method('getBaseUrl')
            ->with('media');

        $this->coreFileStorageDatabaseMock
            ->expects($this->never())
            ->method('saveFile');

        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);

        $this->imageUploaderObject->saveFileToTmpDir($fileId);
    }
}
