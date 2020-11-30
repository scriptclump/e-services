/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('sponsors', {
    sponsor_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    sponsor_name: {
      type: DataTypes.STRING(64),
      allowNull: false
    },
    size: {
      type: DataTypes.STRING(10),
      allowNull: true
    },
    sort_order: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    navigate_object_id: {
      type: DataTypes.STRING(11),
      allowNull: false
    },
    navigator_objects: {
      type: DataTypes.STRING(50),
      allowNull: false
    },
    frequency: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      defaultValue: '0'
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    hub_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    beat_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    impression_cost: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    click_cost: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    from_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    to_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    status: {
      type: DataTypes.INTEGER(1),
      allowNull: false,
      defaultValue: '0'
    },
    created_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: true,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    display_type: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    }
  }, {
    tableName: 'sponsors'
  });
};
