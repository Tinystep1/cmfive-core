<?php use Carbon\Carbon; ?>

<?php if (!empty($status)) : ?>
    <h4><?php echo $status; ?></h4>
<?php endif; ?>

<div class="tabs">
    <div class="tab-head">
        <a href="#batch">Batch</a>
        <a href="#individual">Individual</a>
        <a href="#seed">Database Seeds</a>
    </div>
    <div class="tab-body clearfix">
        <div id="batch">
            <div class="row-fluid">
                <?php echo Html::b("/admin-migration/rollbackbatch", "Rollback latest batch", "Are you sure you want to rollback migrations?"); ?>
            </div>
            <ul class="accordion" data-accordion>
                <?php if (!empty($not_installed)) : ?>
                    <li class="accordion-navigation">
                        <a href="#batch_available">Not Installed</a>
                        <div id="batch_available" class="content active">
                            <center>
                            <?php
                                echo Html::b("/admin-migration/run/all?ignoremessages=false&prevpage=batch", "Install migrations", "Are you sure you want to install migrations?", null, false, "right");
                            ?>
                            </center>
                                <?php
                                $header = ["Name", "Description", "Pre Text", "Post Text"];
                                $data = [];
                                foreach ($not_installed as $module => $_not_installed) {
                                    foreach ($_not_installed as $_migration_class) {
                                        $migration_path = $_migration_class['path'];
                                        if (file_exists(ROOT_PATH . '/' . $migration_path)) {
                                            include_once ROOT_PATH . '/' . $migration_path;
                                            $classname = $_migration_class['class']['class_name'];
                                            if (class_exists($classname)) {
                                                $migration = (new $classname(1))->setWeb($w);
                                                $migration_description = $migration->description();
                                                $migration_preText = $migration->preText();
                                                $migration_postText = $migration->postText();
                                            }
                                            $row = [];
                                            $row[] = $module . ' - ' . $classname;
                                            $row[] = $migration_description;
                                            $row[] = $migration_preText;
                                            $row[] = $migration_postText;
                                            $data[] = $row;
                                        }
                                    }
                                }

                                $table =  Html::table($data, null, "tablesorter", $header);
                                echo $table;
                                ?>
                        </div>
                    </li>
                <?php endif;
                if (!empty($batched)) :
                    krsort($batched);
                    foreach ($batched as $batch_no => $batched_migrations) : ?>
                        <li class="accordion-navigation">
                            <a href="#batch_<?php echo $batch_no; ?>">Batch <?php echo $batch_no; ?></a>
                            <div id="batch_<?php echo $batch_no; ?>" class="content">
                                <table style width="100%">
                                    <?php
                                    $header = ["Name", "Description", "Pre Text", "Post Text"];
                                    $data = [];
                                    foreach ($batched_migrations as $batched_migration) {
                                        $row = [];
                                        $row[] = $batched_migration['module'] . ' - ' . $batched_migration['classname'];
                                        $row[] = $batched_migration['description'];
                                        $row[] = $batched_migration['pretext'];
                                        $row[] = $batched_migration['posttext'];
                                        $data[] = $row;
                                    }
                                    $table =  Html::table($data, null, "tablesorter", $header);
                                    echo $table;
                                    ?>
                    <?php endforeach; ?>
                                </table>
                            </div>
                        </li>
                <?php endif; ?>
            </ul>
        </div>
        <div id="individual">
            <?php if (!empty($available)) : ?>
                <ul id="migrations_list" class="tabs vertical" style="border: 1px solid #444;" data-tab>
                    <?php foreach ($available as $module => $available_in_module) : ?>
                        <li class="tab-title">
                            <a href="#<?php echo $module; ?>">
                                <?php echo ucfirst($module); ?>
                                <div class="right">
                                    <?php
                                    echo count(@$installed[$module]) > 0 ? "<span class='label round success' style='font-size: 14pt;'>" . count($installed[$module]) . "</span>" : "";
                                    echo (count($available_in_module) - count(@$installed[$module]) > 0 ? '<span class="label round warning" style="font-size: 14pt;">' . (count($available_in_module) - count(@$installed[$module])) . '</span>' : '');
                                    ?>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tabs-content">
                    <?php foreach ($available as $module => $available_in_module) : ?>
                        <div class="content" style="padding-top: 0px;" id="<?php echo $module; ?>">
                            <?php echo Html::box("/admin-migration/create/" . $module, "Create a" . (in_array($module{0}, ['a', 'e', 'i' ,'o', 'u']) ? 'n' : '') . ' ' . $module . " migration", true); ?>
                            <?php if (count($available[$module]) > 0) : ?>
                                <?php echo Html::b("/admin-migration/run/" . $module . "?ignoremessages=false&prevage=individual", "Run all " . $module . " migrations", "Are you sure you want to run all outstanding migrations for this module?"); ?>
                                <table>
                                    <thead>
                                        <tr><th>Name</th><th>Description</th><th>Path</th><th>Date run</th><th>Pre Text</th><th>Post Text</th><th>Actions</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($available_in_module as $a_migration_path => $migration_data) : ?>
                                        <tr <?php echo ($w->Migration->isInstalled($migration_data['class_name'])) ? 'style="background-color: #43CD80;"' : ''; ?>>
                                            <td><?php echo $migration_data['class_name']; ?></td>
                                            <td>
                                            <?php
                                                echo $migration_data['description'];
                                            ?>
                                            </td>
                                            <td><?php echo $a_migration_path; ?></td>
                                            <td>
                                                <?php if ($w->Migration->isInstalled($migration_data['class_name'])) :
                                                    $installedMigration = $w->Migration->getMigrationByClassname($migration_data['class_name']); ?>
                                                    <span data-tooltip aria-haspopup="true" title="<?php echo @formatDate($installedMigration->dt_created, "d-M-Y \a\\t H:i"); ?>">
                                                        Run <?php echo Carbon::createFromTimeStamp($installedMigration->dt_created)->diffForHumans(); ?> by <?php echo !empty($installedMigration->creator_id) && !empty($w->Auth->getUser($installedMigration->creator_id)) ? $w->Auth->getUser($installedMigration->creator_id)->getContact()->getFullName() : "System"; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                            <?php

                                                echo $migration_data['pretext'];
                                            ?>
                                            </td>
                                            <td>
                                                <?php
                                                    echo $migration_data['posttext'];
                                                ?>
                                            </td>
                                            <td>
                                            <?php
                                            $filename = basename($a_migration_path, ".php");
                                            if ($w->Migration->isInstalled($migration_data['class_name'])) {
                                                echo Html::b('/admin-migration/rollback/' . $module . '/' . $filename, "Rollback to here", "Are you 110% sure you want to rollback a migration? DATA COULD BE LOST PERMANENTLY!", null, false, "warning expand");
                                            } else {
                                                echo Html::b('/admin-migration/run/' . $module . '/' . $filename. "?ignoremessages=false&prevpage=individual", "Migrate to here", "Are you sure you want to run a migration?", null, false, "info expand");
                                            }
                                            ?>
                                            </td>

                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <script>
                    $(document).ready(function() {
                        $("ul#migrations_list li:visible").click(function() {
                            window.location.hash = $('a', $(this)).attr('href');
                        });

                        var tab_item;
                        if (window.location.hash) {
                            $("ul#migrations_list li:visible").each(function(index, element) {
                                if ($('a', element).attr('href') == window.location.hash) {
                                    $(element).addClass('active');
                                    tab_item = $(element);
                                }
                            });
                        } else {
                            tab_item = $("ul#migrations_list li:visible").first();
                            tab_item.addClass("active");
                        }

                        if (tab_item) {
                            var element_href = tab_item.find('a').attr('href');
                            $(".tabs-content #" + element_href.substring(1, element_href.length).toLowerCase()).addClass("active");
                        }
                    });
                </script>
            <?php else : ?>
                <h4>There are no migrations on this project</h4>
            <?php endif; ?>
        </div>
        <div id='seed'>
            <?php echo Html::box('/admin-migration/createseed', 'Create a seed', true); ?>
            <?php if (!empty($seeds)) : ?>
                <ul class="tabs" data-tab>
                    <?php foreach ($seeds as $module => $available_seeds) : ?>
                        <?php if (count($available_seeds) > 0) : ?>
                            <li class="tab-title <?php echo key($seeds) == $module ? 'active': '' ?>">
                                <a href="#<?php echo $module; ?>"><?php echo ucfirst($module); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="tabs-content">
                    <?php foreach ($seeds as $module => $available_seeds) : ?>
                        <div class="content <?php echo key($seeds) == $module ? 'active': '' ?>" id="<?php echo $module; ?>">
                            <table class='small-12 columns'>
                                <thead>
                                    <tr>
                                        <td>Name</td>
                                        <td>Description</td>
                                        <td>Status</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($available_seeds as $seed => $classname) : ?>
                                        <?php if (is_file($seed)) {
                                            require_once($seed);

                                            $seed_obj = null;
                                            if (class_exists($classname)) {
                                                $seed_obj = new $classname($w);
                                            }
                                        }
                                        if (!empty($seed_obj)) :
                                            $migration_exists = $w->Migration->migrationSeedExists($classname); ?>
                                            <tr>
                                                <td><?php echo $seed_obj->name; ?></td>
                                                <td><?php echo $seed_obj->description; ?></td>
                                                <td>
                                                    <?php if ($migration_exists) : ?>
                                                        <span class='label success'>Installed</span>
                                                    <?php else : ?>
                                                        <span class='label secondary'>Not installed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php echo !$migration_exists ? Html::b('/admin-migration/installseed?url=' . urlencode($seed), 'Install') : ''; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
