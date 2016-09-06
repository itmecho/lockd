<?php

class DefaultFolderRepositoryTest extends TestCase
{
    /** @var \Lockd\Repositories\DefaultFolderRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->repository = new \Lockd\Repositories\DefaultFolderRepository();
    }

    public function tearDown()
    {
        unset($this->repository);
        parent::tearDown();
    }

    public function testFind()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create();

        $results = $this->repository->find();

        $this->assertCount(3, $results);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $results[0]);
        $this->assertEquals($this->ee['folder1']->id, $results[0]->id);
        $this->assertEquals($this->ee['folder1']->name, $results[0]->name);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $results[1]);
        $this->assertEquals($this->ee['folder2']->id, $results[1]->id);
        $this->assertEquals($this->ee['folder2']->name, $results[1]->name);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $results[2]);
        $this->assertEquals($this->ee['folder3']->id, $results[2]->id);
        $this->assertEquals($this->ee['folder3']->name, $results[2]->name);
    }

    public function testFindWithParameters()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create();

        $results = $this->repository->find([
            'name' => $this->ee['folder2']->name,
        ]);

        $this->assertCount(1, $results);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $results[0]);
        $this->assertEquals($this->ee['folder2']->id, $results[0]->id);
        $this->assertEquals($this->ee['folder2']->name, $results[0]->name);
    }

    public function testFindOneById()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create();

        $result = $this->repository->findOneById($this->ee['folder2']->id);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $result);
        $this->assertEquals($this->ee['folder2']->id, $result->id);
        $this->assertEquals($this->ee['folder2']->name, $result->name);
    }

    public function testFindOneByIdNotFound()
    {
        $this->assertNull($this->repository->findOneById(1000));
    }

    public function testFindSubFolders()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create([
            'parent_id' => $this->ee['folder1']->id
        ]);
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create([
            'parent_id' => $this->ee['folder1']->id
        ]);

        $results = $this->repository->findSubFolders($this->ee['folder1']);

        $this->assertCount(2, $results);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $results[0]);
        $this->assertEquals($this->ee['folder2']->id, $results[0]->id);
        $this->assertEquals($this->ee['folder2']->name, $results[0]->name);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $results[1]);
        $this->assertEquals($this->ee['folder3']->id, $results[1]->id);
        $this->assertEquals($this->ee['folder3']->name, $results[1]->name);
    }

    public function testFindSubFoldersNoFolders()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();

        $results = $this->repository->findSubFolders($this->ee['folder1']);

        $this->assertCount(0, $results);
    }

    public function testFindParent()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create([
            'parent_id' => $this->ee['folder1']->id,
        ]);

        $result = $this->repository->findParent($this->ee['folder2']);

        $this->assertInstanceOf(\Lockd\Models\Folder::class, $result);
        $this->assertEquals($this->ee['folder1']->id, $result->id);
        $this->assertEquals($this->ee['folder1']->name, $result->name);
    }

    public function testCount()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create();

        $result = $this->repository->count();

        $this->assertEquals(3, $result);
    }

    public function testCountNoResults()
    {
        $result = $this->repository->count();

        $this->assertEquals(0, $result);
    }

    public function testCountSubFolders()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();
        $this->ee['folder2'] = factory(\Lockd\Models\Folder::class)->create([
            'parent_id' => $this->ee['folder1']->id
        ]);
        $this->ee['folder3'] = factory(\Lockd\Models\Folder::class)->create([
            'parent_id' => $this->ee['folder1']->id
        ]);

        $result = $this->repository->countSubFolders($this->ee['folder1']);

        $this->assertEquals(2, $result);
    }

    public function testCountSubFoldersNoFolders()
    {
        $this->ee['folder1'] = factory(\Lockd\Models\Folder::class)->create();

        $result = $this->repository->countSubFolders($this->ee['folder1']);

        $this->assertEquals(0, $result);
    }
}