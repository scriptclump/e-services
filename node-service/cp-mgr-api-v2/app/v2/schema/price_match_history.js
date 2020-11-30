/* jshint indent: 2 */

module.exports = function(sequelize, DataTypes) {
  return sequelize.define('price_match_history', {
    history_id: {
      type: DataTypes.INTEGER(11),
      allowNull: false
    },
    article_number: {
      type: DataTypes.STRING(20),
      allowNull: true
    },
    product_id: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    manufacturer: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    product_title: {
      type: DataTypes.STRING(250),
      allowNull: true
    },
    group_id: {
      type: DataTypes.INTEGER(20),
      allowNull: true
    },
    kvi: {
      type: DataTypes.STRING(5),
      allowNull: true
    },
    last_updated: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    soh: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    is_active: {
      type: DataTypes.INTEGER(2),
      allowNull: true
    },
    cfc: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    esu: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    mrp: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    ptr: {
      type: DataTypes.INTEGER(10),
      allowNull: true
    },
    ptr_percentage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    gst_percentage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    base_rate: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    base_rate-sch_amt: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    net_rate: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    ebt_margin_percentage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    extra: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    esp: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    elp_percentage: {
      type: DataTypes.DECIMAL,
      allowNull: true
    },
    created_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    created_by: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_at: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    },
    updated_by: {
      type: DataTypes.DATE,
      allowNull: false,
      defaultValue: sequelize.literal('CURRENT_TIMESTAMP')
    }
  }, {
    tableName: 'price_match_history'
  });
};
