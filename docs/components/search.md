Search-Component
==============

The Search-Component allows us to add a search-engine to our list. This works smoothly with the PaginatorComponent of CakePHP.

[doc_toc]

Loading
--------

You can load the component in your `AppController`:

    public function initialize() {
        // code
        $this->loadComponent('Utils.Search');

        $this->helpers[] = 'Utils.Search';
    }
    
Configuring
-----------

The component handles muliple filters wich will contain default settings. The default settings can be changed with:

    $this->Search->config('_default', []);

Default settings:

- Column - The column where the search will be done to. Default depends on the added filter.
- Operator - The operator like '=' or 'LIKE'. Default set to 'LIKE'.
- Attributes - Attributes for the form. Default configurations are set here.
- Options - An option-list when you want a select-box.


Usage
-------------------

### Adding filters

You can add filters in your action-method like:

    $this->Search->addFilter('username');

In this case there will be add a filter to search on the column 'username'.
To change te column-name, use:

    $this->Search->addFilter('username', [
        'column' => 'custom_column',
    ]);

> Note: You are not able YET to search on multiple columns.

The previous examples will create a text-box where you can type your commands. But what if we want to add a selectbox?
Imagine we want to search in our articles and want to add a searchbox for the categories:

    $this->Search->addFilter('category_id', [
        'options' => $this->Articles->Categories->find('list')->toArray(),
    ]);

This is all you need, this will create a selectbox to select a category to search on.

    
### Removing filters

Maybe you want to remove filters. We've build the following method to do that:

    $this->Search->removeFilter('username');


### Setting up the query

You've added your filters, but they aren't used yet. Using the `search` method will expand our current query:

    // add your filters

    $users = $this->Users->find('all', ['contain' => ['Roles']]);

    $search = $this->Search->search($users);

    $this->set(compact('search'));

In this example we pushed the variable `$users` to the `search` method. This method returned a modified Query-object.

It is very easy to integrate the PaginatorComponent:

    // add your filters

    $users = $this->Users->find('all', ['contain' => ['Roles']]);

    $search = $this->Search->search($users);

    $paginated = $this->paginate($search);

    $this->set(compact('paginated'));

Now, the query we created at `$users` is modified by the SearchComponent AND the Paginator.


Building the form
-------------------

Now you need a form in your page. For that we need the `SearchHelper`. This is the only rule of code you need in your html-file:
    
    <?= $this->Search->filterForm($searchFilters) ?>

This piece of code will generate a form, based on the filters stored in the `$searchFilters` variable. This variable is automatically set by the SearchComponent.