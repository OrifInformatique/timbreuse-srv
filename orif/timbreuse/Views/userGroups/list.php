<div class="container">
    <div class="row mb-2">
        <div class="text-left col-12">
            <h3><?= ucfirst(lang('tim_lang.user_group_list')) ?></h3>
        </div>
        <div class="col-sm-6 text-left">
            <a class="btn btn-primary" href="<?= current_url() . '/create' ?>"><?= lang('common_lang.btn_add') ?></a>
        </div>
    </div>

    <table class="table table-striped table-hover tree-table">
        <thead>
            <th><?= lang('tim_lang.field_name') ?></th>
            <th></th>
        </thead>
        <tbody>
            <?php foreach($userGroups as $userGroup): ?>
                <tr class="<?= $userGroup['class'], str_contains($userGroup['class'], 'child') ? ' d-none' : '' ?>">
                    <td><?= str_contains($userGroup['class'], 'parent') ? '<i class="bi bi-chevron-down toggle-arrow"> ' : '' ?><?= $userGroup['name'] ?></td>
                    <td class="text-right">
                        <a href="<?= current_url() . "/update/{$userGroup['id']}" ?>">
                        <i class="bi bi-pencil" style="font-size: 20px;"></i>
                        </a>
                        <a href="<?= current_url() . "/delete/{$userGroup['id']}" ?>">
                            <i class="bi bi-trash" style="font-size: 20px;"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<script>
    document.querySelectorAll(".toggle-arrow").forEach(function(arrow) {
        arrow.addEventListener("click", function() {
            var row = this.closest(".parent");
            const allChildren = getAllChildrenUnderParent(row);

            allChildren.forEach((child) => {
                child.classList.toggle('d-none');
            });

            const firstChild = row.nextElementSibling;
            const classes = this.classList;
            if (firstChild.classList.contains("d-none")) {
                classes.remove("bi-chevron-up");
                classes.add("bi-chevron-down");
            } else {
                classes.remove("bi-chevron-down");
                classes.add("bi-chevron-up");
            }
        });
    });

    function getAllChildrenUnderParent(parent) {
        const children = [];
        let currentElement = parent.nextElementSibling;
        while (currentElement && currentElement.classList.contains("child")) {
            children.push(currentElement);

            if (currentElement.classList.contains("parent")) {
                if (!currentElement.nextElementSibling.classList.contains("parent")) {
                    break;
                }
            }

            currentElement = currentElement.nextElementSibling;
        }

        return children;
    }
</script>