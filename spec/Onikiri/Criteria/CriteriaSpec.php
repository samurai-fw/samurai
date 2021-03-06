<?php

namespace spec\Samurai\Onikiri\Criteria;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Onikiri;
use Samurai\Onikiri\EntityTable;

class CriteriaSpec extends PHPSpecContext
{
    public function let(EntityTable $t)
    {
        $t->getTableName()->willReturn('foo');
        $this->beConstructedWith($t);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Criteria\Criteria');
    }

    public function it_set_entity_table(EntityTable $t)
    {
        $this->setTable($t);
        $this->getTable()->shouldBe($t);
    }


    public function it_is_where_by_most_simple()
    {
        $this->where('id = ?', 1);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id = ?)');
        $this->getParams()->shouldBe([1]);
    }

    public function it_is_where_multiple_conditions()
    {
        $this->where('id = ? or id = ?', 1, 2)->andAdd('name = :name1 OR name = :name2', [':name1' => 'kaneda', ':name2' => 'tetsuo']);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id = ? or id = ?) AND (name = :name1 OR name = :name2)');
        $this->getParams()->shouldBe([1, 2, ':name1' => 'kaneda', ':name2' => 'tetsuo']);
    }

    public function it_is_where_or_condition()
    {
        $this->where('id = ?', 1)->orAdd('name = :name1 OR name = :name2', [':name1' => 'kaneda', ':name2' => 'tetsuo']);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id = ?) OR (name = :name1 OR name = :name2)');
        $this->getParams()->shouldBe([1, ':name1' => 'kaneda', ':name2' => 'tetsuo']);
    }

    public function it_is_where_not_condition()
    {
        $this->where('id = ?', 1)->andNot('name = :name', [':name' => 'kaneda']);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id = ?) AND ! (name = :name)');
        $this->getParams()->shouldBe([1, ':name' => 'kaneda']);
    }

    public function it_is_where_in_condition_directly()
    {
        $this->whereIn('id', [1, 2, 3]);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id IN (?, ?, ?))');
        $this->getParams()->shouldBe([1, 2, 3]);
    }

    public function it_is_where_in_condition_or()
    {
        $this->whereIn('id', [1, 2, 3])->orIn('id', [4, 5, 6]);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id IN (?, ?, ?)) OR (id IN (?, ?, ?))');
        $this->getParams()->shouldBe([1, 2, 3, 4, 5, 6]);
    }
    
    public function it_is_where_notin_condition_directly()
    {
        $this->whereNotIn('id', [1, 2, 3]);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id NOT IN (?, ?, ?))');
        $this->getParams()->shouldBe([1, 2, 3]);
    }

    public function it_is_where_between_condition_directly()
    {
        $this->whereBetween('id', 1, 10);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id BETWEEN ? AND ?)');
        $this->getParams()->shouldBe([1, 10]);
    }
    
    public function it_is_where_between_condition_or()
    {
        $this->whereBetween('id', 1, 10)->orBetween('id', 20, 50);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id BETWEEN ? AND ?) OR (id BETWEEN ? AND ?)');
        $this->getParams()->shouldBe([1, 10, 20, 50]);
    }

    public function it_is_where_notbetween_condition_directly()
    {
        $this->whereNotBetween('id', 1, 10);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (id NOT BETWEEN ? AND ?)');
        $this->getParams()->shouldBe([1, 10]);
    }
    
    public function it_is_where_like_condition_directly()
    {
        $this->whereLike('alias', 'foo.%');
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (alias LIKE ?)');
        $this->getParams()->shouldBe(['foo.%']);
    }
    
    public function it_is_where_like_condition_or()
    {
        $this->whereLike('alias', 'foo.%')->orLike('bar', '%.zoo.%');
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (alias LIKE ?) OR (bar LIKE ?)');
        $this->getParams()->shouldBe(['foo.%', '%.zoo.%']);
    }

    public function it_is_where_notlike_condition_directly()
    {
        $this->whereNotLike('alias', 'foo.%');
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (alias NOT LIKE ?)');
        $this->getParams()->shouldBe(['foo.%']);
    }
    
    public function it_is_columns_condition()
    {
        $this->columns('id', 'name');
        $this->toSQL()->shouldBe('SELECT id, name FROM foo WHERE 1');
    }
    
    public function it_is_group_condition()
    {
        $this->groupBy('category');
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 GROUP BY category');
    }
    public function it_is_having_condition()
    {
        $this->groupBy('category')->having('COUNT(1) > ?', 1);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 GROUP BY category HAVING (COUNT(1) > ?)');
    }

    public function it_is_order_condition()
    {
        $this->orderBy('created_at DESC');
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 ORDER BY created_at DESC');
    }
    
    public function it_is_order_condition_by_field()
    {
        $this->orderByField('category', ['category1', 'category2', 'category3']);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 ORDER BY FIELD(category, ?, ?, ?)');
        $this->getParams()->shouldBe(['category1', 'category2', 'category3']);
    }

    public function it_is_not_order_by_field_when_empty_params()
    {
        $this->orderByField('category', []);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1');
    }

    public function it_is_limit_condition()
    {
        $this->limit(10);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 LIMIT ?');
        $this->getParams()->shouldBe([10]);
    }
    
    public function it_is_limit_condition_with_offset()
    {
        $this->limit(10)->offset(20);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 LIMIT ? OFFSET ?');
        $this->getParams()->shouldBe([10, 20]);
    }
    
    public function it_is_limit_condition_with_offset_and_where()
    {
        $this->where('type = ?', 'foo')->limit(10)->offset(20);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE (type = ?) LIMIT ? OFFSET ?');
        $this->getParams()->shouldBe(['foo', 10, 20]);
    }

    public function it_is_limit_condition_by_page()
    {
        $this->limit(10)->page(4);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 LIMIT ? OFFSET ?');
        $this->getParams()->shouldBe([10, 30]);
    }

    public function it_is_lock_condition()
    {
        $this->lock();
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 FOR UPDATE');
    }
    public function it_is_lock_shared_condition()
    {
        $this->lock(Onikiri::LOCK_IN_SHARED);
        $this->toSQL()->shouldBe('SELECT * FROM foo WHERE 1 LOCK IN SHARE MODE');
    }


    public function it_bredges_to_talbe_find(EntityTable $t)
    {
        $t->find($this)->shouldBeCalled();
        $this->where('id = ? AND name = ?', [1, 'Satoshinosuke'])->find();
    }
    
    public function it_bredges_to_talbe_findAll(EntityTable $t)
    {
        $t->findAll($this)->shouldBeCalled();
        $this->where('id = ? AND name = ?', [1, 'Satoshinosuke'])->findAll();
    }

    public function it_bredges_to_talbe_update(EntityTable $t)
    {
        $t->update(['name' => 'Kiuchi Satoshinosuke'], $this)->shouldBeCalled();
        $this->where('id = ? AND name = ?', [1, 'Satoshinosuke'])->update(['name' => 'Kiuchi Satoshinosuke']);
    }
    
    public function it_bredges_to_talbe_delete(EntityTable $t)
    {
        $t->delete($this)->shouldBeCalled();
        $this->where('id = ? AND name = ?', [1, 'Satoshinosuke'])->delete();
    }


    public function it_converts_to_insert_sql()
    {
        $this->toInsertSQL(['name' => 'Satoshinosuke', 'lover' => 'Minka'])
            ->shouldBe("INSERT INTO foo (name, lover) VALUES (?, ?)");
        $this->getParams()->shouldBe(['Satoshinosuke', 'Minka']);
    }
    
    public function it_converts_to_update_sql()
    {
        $this->where('id = ?', 1)->toUpdateSQL(['name' => 'Satoshinosuke', 'lover' => 'Minka'])
            ->shouldBe("UPDATE foo SET name = ?, lover = ? WHERE (id = ?)");
        $this->getParams()->shouldBe(['Satoshinosuke', 'Minka', 1]);
    }
    
    public function it_converts_to_delete_sql()
    {
        $this->where('id = ?', 1)->toDeleteSQL()
            ->shouldBe("DELETE FROM foo WHERE (id = ?)");
        $this->getParams()->shouldBe([1]);
    }
}

