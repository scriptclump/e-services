/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('wh_serviceables', {
    wh_serviceables_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false,
      primaryKey: true
    },
    legal_entity_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true,
      references: {
        model: 'legal_entities',
        key: 'legal_entity_id'
      }
    },
    le_wh_id: {
      type: DataTypes.INTEGER(11),
      allowNull: true
    },
    pincode: {
      type: DataTypes.STRING(20),
      allowNull: true,
      unique: true
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
    }
  }, {
    tableName: 'wh_serviceables'
  });
};
