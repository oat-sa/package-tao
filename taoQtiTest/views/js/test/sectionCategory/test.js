define([
    'lodash',
    'taoQtiTest/controller/creator/helpers/sectionCategory',
    'core/errorHandler'
], function (_, sectionCategory, errorHandler){

    'use strict';

    var _sectionModel = {
        'qti-type' : 'assessmentSection',
        sectionParts : [
            {
                'qti-type' : 'assessmentItemRef',
                categories : ['A', 'B']
            },
            {
                'qti-type' : 'assessmentItemRef',
                categories : ['A', 'B']
            },
            {
                'qti-type' : 'assessmentItemRef',
                categories : ['A', 'B', 'C', 'D']
            },
            {
                'qti-type' : 'assessmentItemRef',
                categories : ['A', 'B', 'D', 'E', 'F']
            }
        ]
    };

    QUnit.test('isValidSectionModel', function (assert){

        assert.ok(sectionCategory.isValidSectionModel({
            'qti-type' : 'assessmentSection',
            sectionParts : []
        }));

        assert.ok(sectionCategory.isValidSectionModel(_sectionModel));

        assert.ok(!sectionCategory.isValidSectionModel({
            'qti-type' : 'assessmentItemRef',
            categories : ['A', 'B', 'C', 'D']
        }));

        assert.ok(!sectionCategory.isValidSectionModel({
            'qti-type' : 'assessmentSection',
            noSectionParts : null
        }));

    });

    QUnit.test('getCategories', function (assert){
        var sectionModel = _.cloneDeep(_sectionModel);
        var categories = sectionCategory.getCategories(sectionModel);
        assert.deepEqual(categories.all, ['A', 'B', 'C', 'D', 'E', 'F'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'B'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');
    });

    QUnit.test('addCategories', function (assert){

        var sectionModel = _.cloneDeep(_sectionModel);
        var categories = sectionCategory.getCategories(sectionModel);

        assert.deepEqual(categories.all, ['A', 'B', 'C', 'D', 'E', 'F'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'B'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');

        //add a new category
        sectionCategory.addCategories(sectionModel, ['G']);
        categories = sectionCategory.getCategories(sectionModel);
        assert.deepEqual(categories.all, ['A', 'B', 'C', 'D', 'E', 'F', 'G'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'B', 'G'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');

        //try adding an exiting one
        sectionCategory.addCategories(sectionModel, ['A', 'C']);
        assert.deepEqual(categories.all, ['A', 'B', 'C', 'D', 'E', 'F', 'G'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'B', 'G'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');
    });

    QUnit.test('removeCategories', function (assert){

        var sectionModel = _.cloneDeep(_sectionModel);
        var categories = sectionCategory.getCategories(sectionModel);

        assert.deepEqual(categories.all, ['A', 'B', 'C', 'D', 'E', 'F'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'B'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');

        //remove one element from the propagated categories
        sectionCategory.removeCategories(sectionModel, ['A']);
        categories = sectionCategory.getCategories(sectionModel);
        assert.deepEqual(categories.all, ['B', 'C', 'D', 'E', 'F'], 'all categories found');
        assert.deepEqual(categories.propagated, ['B'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');

        //remove one element from the partial categories
        sectionCategory.removeCategories(sectionModel, ['F']);
        categories = sectionCategory.getCategories(sectionModel);
        assert.deepEqual(categories.all, ['B', 'C', 'D', 'E'], 'all categories found');
        assert.deepEqual(categories.propagated, ['B'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E'], 'partial categories found');

        //remove one element on each group of categories (propagated+partial)
        sectionCategory.removeCategories(sectionModel, ['B', 'D']);
        categories = sectionCategory.getCategories(sectionModel);
        assert.deepEqual(categories.all, ['C', 'E'], 'all categories found');
        assert.deepEqual(categories.propagated, [], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'E'], 'partial categories found');
    });


    QUnit.test('setCategories', function (assert){

        var sectionModel = _.cloneDeep(_sectionModel);
        var categories = sectionCategory.getCategories(sectionModel);

        assert.deepEqual(categories.all, ['A', 'B', 'C', 'D', 'E', 'F'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'B'], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D', 'E', 'F'], 'partial categories found');
        
        //remove B, E and F and add G
        sectionCategory.setCategories(sectionModel, ['A', 'C', 'D', 'G']);
        
        //check result
        categories = sectionCategory.getCategories(sectionModel);
        assert.deepEqual(categories.all, ['A', 'C', 'D', 'G'], 'all categories found');
        assert.deepEqual(categories.propagated, ['A', 'G' ], 'propagated categories found');
        assert.deepEqual(categories.partial, ['C', 'D'], 'partial categories found');
    });
});